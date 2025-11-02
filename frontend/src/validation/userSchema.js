// src/validation/userSchema.js
import * as yup from 'yup'

// 🔸 登録用（ユーザー名＋メール＋パスワード）
export const registerSchema = yup.object({
    name: yup
        .string()
        .required('ユーザー名は必須です')
        .max(20, 'ユーザー名は20文字以内で入力してください'),
    email: yup
        .string()
        .required('メールアドレスは必須です')
        .email('メールアドレスの形式で入力してください'),
    password: yup
        .string()
        .required('パスワードは必須です')
        .min(6, 'パスワードは6文字以上で入力してください'),
    })

    // 🔸 ログイン用（メール＋パスワードのみ）
    export const loginSchema = yup.object({
    email: yup
        .string()
        .required('メールアドレスは必須です')
        .email('メールアドレスの形式で入力してください'),
    password: yup
        .string()
        .required('パスワードは必須です'),
})
