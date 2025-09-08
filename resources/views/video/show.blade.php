<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Terabox Video</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }
    .container {
      max-width: 800px;
      margin: auto;
    }
    iframe {
      width: 100%;
      height: 480px;
      border: none;
      margin-top: 15px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Terabox Video</h1>

    <p>
      Original URL:
      <a href="{{ $video->original_url }}" target="_blank">
        {{ $video->original_url }}
      </a>
    </p>

    <p>
      If the video doesn’t load below, please click the original link above to watch it on Terabox.
    </p>

    {{-- Try to embed the original link --}}
    <iframe src="{{ $video->original_url }}" allowfullscreen></iframe>
  </div>
</body>
</html>
