<template>
  <header class="auth-header">
    <div class="logo-area">
      <img src="@/assets/logo.png" alt="SHAREロゴ" class="logo" />
    </div>

    <nav class="nav-links">
      <RouterLink to="/register" class="link">新規登録</RouterLink>
      <RouterLink to="/login" class="link">ログイン</RouterLink>
    </nav>
  </header>
</template>

<style scoped>
.auth-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #141a20;
  padding: 20px 40px;
}

.logo {
  height: 40px;
}

.nav-links {
  display: flex;
  gap: 20px;
}

.link {
  color: #fff;
  font-size: 14px;
  text-decoration: none;
  transition: opacity 0.2s ease;
}

.link:hover {
  opacity: 0.7;
}
</style>
