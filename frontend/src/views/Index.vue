<template>
  <div class="home-bg">
    <div class="home-container">
      <!-- ✅ サイドメニュー -->
      <SideNav @share="addMessage" />

      <!-- ✅ メインコンテンツ -->
      <main class="main-content">
        <h2 class="main-title">ホーム</h2>

        <!-- ✅ 投稿リスト -->
        <Message
          v-for="msg in messages"
          :key="msg.id"
          :id="msg.id"
          :username="msg.username"
          :text="msg.text"
          :likes="msg.likes"
          :comments="msg.comments"
        />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/plugins/axios";
import Message from "@/components/Message.vue";
import SideNav from "@/components/SideNav.vue";

const messages = ref([]);
const loading = ref(true);
const errorMsg = ref("");

// ✅ 投稿一覧を時系列で取得
onMounted(async () => {
  try {
    const res = await api.get("/posts");
    messages.value = Array.isArray(res.data)
      ? res.data.map((p) => ({
          id: p.id,
          username: p.user?.name || "ゲスト",
          text: p.text,
          likes: p.likes,
          comments: p.comments || [],
        }))
      : [];
  } catch (error) {
    console.error("投稿取得エラー:", error);
    errorMsg.value = "投稿を読み込めませんでした。";
  } finally {
    loading.value = false;
  }
});

// ✅ 新規投稿
const addMessage = async (newText) => {
  if (!newText?.trim()) return;

  try {
    const res = await api.post("/posts", { text: newText });
    messages.value.unshift({
      id: res.data.id,
      username: "あなた",
      text: res.data.text,
      likes: res.data.likes,
      comments: [],
    });

    // ✅ 投稿後、SideNav の入力欄をリセット
    window.dispatchEvent(new Event("clear-share-input"));
  } catch (error) {
    if (error.response?.status === 409) {
      alert("同じ内容の投稿は短時間にできません。");
    } else if (error.response?.status === 401) {
      alert("ログインが必要です。");
    } else {
      console.error("投稿追加エラー:", error);
    }
  }
};
</script>

<style scoped>
.home-bg {
  background-color: #141a20;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-top: 40px;
}

.home-container {
  display: flex;
  width: 1000px;
  gap: 40px;
}

.main-content {
  flex: 1;
  color: white;
}

.main-title {
  font-size: 20px;
  font-weight: bold;
  margin-bottom: 20px;
  border-bottom: 1px solid #ccc;
  padding-bottom: 8px;
}
</style>
