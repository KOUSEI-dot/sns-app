<template>
  <div class="detail-bg">
    <div class="detail-container">
      <SideNav />

      <main class="main-content">
        <button class="back-btn" @click="goHome">← ホームに戻る</button>

        <div v-if="post" class="post-box">
          <div class="post-header">
            <strong>{{ post.user?.name || "ゲスト" }}</strong>
            <p class="post-text">{{ post.text }}</p>
          </div>

          <!-- ❤️ いいね -->
          <div class="post-actions">
            <img
              :src="heart"
              class="icon-small"
              :class="{ liked: isLiked }"
              @click="toggleLike"
            />
            <span>{{ likeCount }}</span>
          </div>

          <!-- 💬 コメント一覧 -->
          <div class="comments-section">
            <h3>コメント</h3>
            <div
              v-for="(comment, index) in post.comments"
              :key="index"
              class="comment-item"
            >
              <strong>{{ comment.user?.name || "ゲスト" }}</strong>
              <p>{{ comment.content }}</p>
            </div>

            <!-- ✏️ コメント入力フォーム -->
            <form @submit.prevent="onSubmit" class="comment-form" novalidate>
              <textarea
                v-model="content"
                placeholder="コメントを入力"
                class="comment-input"
              ></textarea>
              <p class="error">{{ contentError }}</p>
              <button
                type="submit"
                class="comment-btn"
                :disabled="isSubmitting"
              >
                コメントする
              </button>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "@/plugins/axios";
import SideNav from "@/components/SideNav.vue";
import heart from "@/assets/heart.png";

// ✅ VeeValidate + Yup
import { useForm, useField } from "vee-validate";
import { commentSchema } from "@/validation/commentSchema";

const router = useRouter();
const route = useRoute();
const post = ref(null);
const likeCount = ref(0);
const isLiked = ref(false);

// ✅ コメントバリデーション設定
const { handleSubmit, isSubmitting } = useForm({
  validationSchema: commentSchema,
});
const { value: content, errorMessage: contentError } = useField("content");

// ✅ 投稿詳細取得
onMounted(async () => {
  try {
    const res = await api.get(`/posts/${route.params.id}`);
    post.value = res.data;
    likeCount.value = res.data.likes;
  } catch (err) {
    console.error("投稿詳細取得エラー:", err);
  }
});

// ✅ コメント投稿処理
const onSubmit = handleSubmit(async (values) => {
  try {
    const res = await api.post(`/posts/${route.params.id}/comments`, {
      text: values.content,
    });
    post.value.comments.push(res.data);
    content.value = "";
  } catch (err) {
    console.error("コメント投稿エラー:", err);
  }
});

// ✅ いいねトグル
const toggleLike = async () => {
  try {
    const res = await api.put(`/posts/${route.params.id}/like`);
    likeCount.value = res.data.likes;
    isLiked.value = res.data.status === "liked";
  } catch (err) {
    console.error("いいねエラー:", err);
  }
};

// ✅ ホームに戻る
const goHome = () => {
  router.push("/");
};
</script>

<style scoped>
.detail-bg {
  background-color: #141a20;
  min-height: 100vh;
  color: white;
}

.detail-container {
  display: flex;
  justify-content: center;
  padding: 40px;
}

.main-content {
  width: 700px;
}

.back-btn {
  background: none;
  color: white;
  border: none;
  cursor: pointer;
  margin-bottom: 20px;
}

.post-box {
  background: #1e2630;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
}

.post-header {
  margin-bottom: 16px;
}

.post-actions {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 20px;
}

.icon-small {
  width: 22px;
  cursor: pointer;
}

.icon-small.liked {
  filter: hue-rotate(-25deg) saturate(4) brightness(1.3);
}

.comments-section {
  margin-top: 20px;
}

.comment-item {
  background: #2a333f;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 10px;
}

.comment-form {
  margin-top: 20px;
}

.comment-input {
  width: 100%;
  height: 80px;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #999;
  background-color: transparent;
  color: white;
  margin-bottom: 6px;
}

.comment-btn {
  background: linear-gradient(135deg, #6a00f4, #8a2be2);
  color: white;
  border: none;
  border-radius: 9999px;
  padding: 8px 24px;
  cursor: pointer;
  font-weight: bold;
}

.error {
  color: #ff5e5e;
  font-size: 13px;
  margin-bottom: 8px;
}
</style>
