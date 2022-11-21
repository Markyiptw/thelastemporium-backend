<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      max-width: 600px;
      background-color: #0d4257;
      color: #eadece;
      font-family: monospace;
      letter-spacing: 0.02em;
      line-height: 1.5;
      display: block;
      margin: 40px auto;
    }

    .signoff {
      width: 300px;
      margin-top: 40px;
      margin-left: auto;
      margin-right: 0px;
    }

    #logo_image {
      display: block;
      margin: 40px auto;
      width: 50%;
    }

    @media (max-width: 767px) {
      body {
        width: 100%;
        padding: 0 20px;
        box-sizing: border-box;
      }

      .signoff {
        width: 60%;
        margin-top: 40px;
        margin-left: auto;
        margin-right: 0px;
      }
    }
  </style>
</head>

<body>
  <p>{!! $messageText !!}</p>
  <p class="signoff">{!! $from !!}</p>
  <p>{!! $timestamp !!}<br>{!! $location !!}</p>
  <a href="https://www.thelastemporium.hk">
    <img id="logo_image" src="{{ Storage::disk('public')->url('assets/the-logo.png') }}">
  </a>
</body>

</html>