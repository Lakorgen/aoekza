<!DOCTYPE html>
<html>
<head>
<title>АО "ЭКЗА"</title>
<meta name="description" content="<? echo $mv -> seo -> description; ?>" />
<meta name="keywords" content="<? echo $mv -> seo -> keywords; ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="<? echo $mv -> media_path; ?>images/icon/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

<link rel="stylesheet" type="text/css" href="<? echo $mv -> media_path; ?>css/style.css" />

<? echo $mv -> seo -> displayMetaData("head"); ?>
</head>
<body>
	<div class="container">
    <header class="header__container header d-flex justify-content-center py-3">
    <button class="burger-menu" id="burger-menu">
        ☰
    </button>
      <ul class="nav nav-pills" id="nav-menu">
        <? echo $mv -> pages -> displayMenu(-1); ?>
      </ul>
    </header>
  </div>

