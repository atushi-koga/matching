<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <title>トップ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/base.css">
  <link rel="stylesheet" href="/css/header.css">
</head>
<body>
<div class="header">
  <div>
    タイトル
  </div>
  <div>
    <ul class="header-menu">
      <li>
        <a href="{{ route('showRegistrationForm') }}">新規会員登録</a>
      </li>
      <li>
        <a href="{{ route('showLoginForm') }}">ログイン</a>
      </li>
    </ul>
  </div>
</div>
