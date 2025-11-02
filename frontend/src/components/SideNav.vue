<template>
  <nav class="side-nav">
    <!-- ロゴ -->
    <img src="@/assets/logo.png" alt="SHARE" class="logo" />

    <!-- メニュー -->
    <ul class="menu">
      <li @click="goHome">
        <img src="@/assets/home.png" class="icon" /> ホーム
      </li>
      <li @click="logout">
        <img src="@/assets/logout.png" class="icon" /> ログアウト
      </li>
    </ul>

    <!-- シェア欄 -->
    <div class="share-area">
      <p class="share-title">シェア</p>

      <!-- ✅ バリデーション付きフォーム -->
      <form @submit.prevent="onSubmit" novalidate>
        <textarea
          v-model="text"
          class="share-text"
          placeholder="いまの気持ちをシェアしよう"
        ></textarea>
        <p class="error">{{ textError }}</p>

        <button
          class="share-btn"
          type="submit"
          :disabled="isSubmitting || isPosting"
        >
          {{ isPosting ? "送信中..." : "シェアする" }}
        </button>

        <p v-if="message" class="error">{{ message }}</p>
      </form>
    </div>
  </nav>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref } from "vue";
import { useRouter } from "vue-router";
import { useForm, useField } from "vee-validate";
import { postSchema } from "@/validation/postSchema"; // ✅ yupスキーマ読み込み
import api from "@/plugins/axios";

const emit = defineEmits(["share"]);
const router = useRouter();

const message = ref("");
const isPosting = ref(false); // ✅ 二重送信防止フラグ

// ✅ VeeValidate設定
const { handleSubmit, isSubmitting } = useForm({
  validationSchema: postSchema,
});
const { value: text, errorMessage: textError } = useField("text");

// ✅ 投稿処理（emitで親へ送信）
const onSubmit = handleSubmit(async (values) => {
  if (isPosting.value) return; // 二重クリック防止
  isPosting.value = true;
  message.value = "";

  try {
    const res = await api.post("/posts", { text: values.text });

    // 親(Home/index.vue)に新しい投稿を通知
    emit("share", res.data.text);

    // 入力欄クリア
    text.value = "";

    // ✅ 投稿完了時にクリアイベントを発火（他コンポーネントにも通知可能）
    window.dispatchEvent(new Event("clear-share-input"));
  } catch (err) {
    console.error("投稿エラー:", err);
    if (err.response?.status === 409) {
      message.value = "同じ内容の投稿は短時間にできません。";
    } else if (err.response?.status === 401) {
      message.value = "ログインが必要です。";
      router.push("/login");
    } else {
      message.value = "投稿に失敗しました。";
    }
  } finally {
    isPosting.value = false;
  }
});

// ✅ 外部イベントでフォームをリセット（index.vueから呼ばれる）
onMounted(() => {
  window.addEventListener("clear-share-input", clearInput);
});
onBeforeUnmount(() => {
  window.removeEventListener("clear-share-input", clearInput);
});

const clearInput = () => {
  text.value = "";
  message.value = "";
};

// ✅ ホームへ戻る
const goHome = () => {
  router.push("/");
};

// ✅ ログアウト
const logout = async () => {
  try {
    await api.post("/logout");
    localStorage.removeItem("token");
    router.push("/login");
  } catch (error) {
    console.error("ログアウトエラー:", error);
  }
};
</script>

<style scoped>
.side-nav {
  width: 260px;
  background-color: #141a20;
  color: white;
}

.logo {
  width: 120px;
  margin-bottom: 30px;
}

.menu {
  list-style: none;
  padding: 0;
  margin: 0 0 40px 0;
}

.menu li {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  font-size: 16px;
  cursor: pointer;
}

.menu li:hover {
  opacity: 0.7;
}

.icon {
  width: 24px;
  margin-right: 10px;
}

.share-title {
  margin-bottom: 10px;
}

.share-text {
  width: 100%;
  height: 100px;
  border-radius: 8px;
  border: 1px solid #999;
  background-color: transparent;
  color: white;
  padding: 10px;
  margin-bottom: 6px;
  resize: none;
}

.share-btn {
  background: linear-gradient(135deg, #6a00f4, #8a2be2);
  color: white;
  border: none;
  border-radius: 9999px;
  padding: 10px 28px;
  cursor: pointer;
  font-weight: bold;
}

.share-btn[disabled] {
  opacity: 0.5;
  cursor: not-allowed;
}

.error {
  color: #ff5e5e;
  font-size: 13px;
  margin-bottom: 6px;
}
</style>
