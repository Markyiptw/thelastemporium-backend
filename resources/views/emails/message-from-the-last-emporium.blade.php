<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="background-color: #0A242E; color: #E8DED0;">
<div style="max-width: 1024px; 	padding-left: 8px; padding-right: 8px; margin-left:auto; margin-right: auto;">
  <p style="white-space: pre-wrap; font-family: monospace; margin-top: 40px;">{{ $messageText }}</p>
  <p style="text-align: right; white-space: pre-wrap; font-family: monospace;">{{ $from }}</p>
  <p style="text-align: right; white-space: pre-wrap; font-family: monospace;">{{ $timestamp }}<br>{{ $location }}</p>
  <a href="https://www.thelastemporium.hk" style="display: block; margin-top: 40px; margin-bottom: 40px;">
    <img
      src="{{ Storage::disk('public')->url('assets/the-logo.png') }}"
      style="display: block; max-width:992px; margin-left:auto; margin-right: auto; width: 100%;"
    >
  </a>
</div>
</body>
</html>
