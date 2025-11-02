<script setup>
import { useRouter } from "vue-router";
import { useForm, useField } from "vee-validate";
import { loginSchema } from "@/validation/userSchema";
import AuthHeader from "@/components/AuthHeader.vue";
import api from "@/plugins/axios";
import { ref } from "vue";

const router = useRouter();
const message = ref("");

// ✅ バリデーション設定
const { handleSubmit, isSubmitting } = useForm({
  validationSchema: loginSchema,
});
const { value: email, errorMessage: emailError } = useField("email");
const { value: password, errorMessage: passwordError } = useField("password");

// ✅ ログイン処理
const onSubmit = handleSubmit(async (values) => {
  try {
    const res = await api.post("/login", {
      email: values.email,
      password: values.password,
    });

    localStorage.setItem("token", res.data.token);
    router.push("/");
  } catch (err) {
    console.error(err);
    message.value = err.response?.data?.message || "ログインに失敗しました。";
  }
});
</script>

<template>
  <div class="auth-bg">
    <AuthHeader />

    <main class="auth-main">
      <div class="auth-card">
        <h2 class="auth-title">ログイン</h2>

        <form @submit.prevent="onSubmit" novalidate>
          <input v-model="email" type="email" placeholder="メールアドレス" />
          <p class="error">{{ emailError }}</p>

          <input v-model="password" type="password" placeholder="パスワード" />
          <p class="error">{{ passwordError }}</p>

          <button type="submit" class="auth-btn" :disabled="isSubmitting">
            ログイン
          </button>

          <p v-if="message" class="error">{{ message }}</p>
        </form>
      </div>
    </main>
  </div>
</template>

<style scoped>
.auth-bg {
  background-color: #141a20;
  min-height: 100vh;
}
.auth-main {
  display: flex;
  justify-content: center;
  align-items: center;
  height: calc(100vh - 80px);
}
.auth-card {
  background-color: #fff;
  padding: 60px 80px;
  border-radius: 10px;
  width: 480px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
}
.auth-title {
  color: #000;
  font-weight: bold;
  font-size: 24px;
  margin-bottom: 32px;
}
input {
  width: 100%;
  padding: 14px 16px;
  margin-bottom: 10px;
  border: 1px solid #333;
  border-radius: 8px;
  font-size: 15px;
}
.error {
  color: red;
  font-size: 13px;
  margin-bottom: 10px;
}
.auth-btn {
  background: linear-gradient(135deg, #6a00f4, #8a2be2);
  color: white;
  border: none;
  border-radius: 9999px;
  padding: 12px 40px;
  cursor: pointer;
  font-weight: bold;
  font-size: 15px;
  transition: opacity 0.2s;
}
.auth-btn:hover {
  opacity: 0.85;
}
</style>
