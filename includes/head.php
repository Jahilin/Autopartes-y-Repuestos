<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $titulo; ?> | Don Pancho</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="css/general.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <?php if (isset($css)) { ?>
        <link href="css/<?php echo $css; ?>.css" rel="stylesheet">
    <?php } ?>
</head>
<body>