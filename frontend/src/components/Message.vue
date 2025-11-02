<template>
  <div class="message">
    <div class="message-header">
      <strong>{{ username }}</strong>
      <div class="icons">
        <!-- ❤️ いいね -->
        <img
          :src="heart"
          class="icon-small"
          :class="{ liked: isLiked }"
          @click="toggleLike"
        />
        <span>{{ likeCount }}</span>

        <!-- ❌ 投稿削除 -->
        <img :src="cross" class="icon-small" @click="deletePost" />

        <!-- ↩ コメント詳細へ遷移 -->
        <img :src="detail" class="icon-small" @click="goToDetail" />
      </div>
    </div>

    <p class="message-text">{{ text }}</p>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import api from "@/plugins/axios";

import heart from "@/assets/heart.png";
import cross from "@/assets/cross.png";
import detail from "@/assets/detail.png";

const router = useRouter();

const props = defineProps({
  id: Number,
  username: String,
  text: String,
  likes: Number,
});

const likeCount = ref(props.likes);
const isLiked = ref(false);

// ❤️ いいねトグル
const toggleLike = async () => {
  try {
    const res = await api.put(`/posts/${props.id}/like`);
    likeCount.value = res.data.likes;
    isLiked.value = res.data.status === "liked";
  } catch (err) {
    console.error("❌ いいねエラー:", err);
  }
};

// ❌ 投稿削除
const deletePost = async () => {
  if (!confirm("この投稿を削除しますか？")) return;

  try {
    await api.delete(`/posts/${props.id}`);
    alert("投稿を削除しました");
    location.reload(); // ← 一時的に再読み込み（後でemit対応可）
  } catch (err) {
    console.error("❌ 投稿削除エラー:", err);
  }
};

// ↩ 投稿詳細ページに遷移
const goToDetail = () => {
  router.push(`/posts/${props.id}`);
};
</script>

<style scoped>
.message {
  border: 1px solid #ccc;
  padding: 16px;
  border-radius: 6px;
  margin-bottom: 20px;
  background-color: #141a20;
  color: white;
}

.message-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #666;
  padding-bottom: 6px;
  margin-bottom: 8px;
}

.icons {
  display: flex;
  align-items: center;
  gap: 8px;
}

.icon-small {
  width: 20px;
  cursor: pointer;
  transition: transform 0.2s, filter 0.2s;
}

.icon-small:hover {
  transform: scale(1.15);
}

/* 💡 いいね済み時に赤色フィルターをかける */
.icon-small.liked {
  filter: hue-rotate(-25deg) saturate(4) brightness(1.3);
}

.message-text {
  margin-top: 10px;
  line-height: 1.6;
}
</style>
