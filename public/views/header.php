<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <!-- meta -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="theme-color" content="#734bff" />

        <!-- title  -->
        <title> <?= app_name(); ?> </title>

        <!-- css -->
        <link rel="stylesheet" href="<?= css_path('master.css'); ?>">
        <link rel="stylesheet" href="<?= css_path('theme.css'); ?>">
        <link rel="stylesheet" href="<?= css_path('custom.css'); ?>">

        <!-- icons -->
        <link rel="stylesheet" href="<?= icons_path('fontawesome/css/all.css'); ?>">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        
        <!-- plugins -->
        <script src="<?= js_path('intializePlugins.js'); ?>" charset="utf-8"></script>

            <!-- jquery -->
            <script src="<?= package_path('jquery/dist/jquery.min.js'); ?>" charset="utf-8"></script>

            <!-- aos animation-->
            <link rel="stylesheet" href="<?= package_path('aos/dist/aos.css'); ?>">
            <script src="<?= package_path('aos/dist/aos.js'); ?>" charset="utf-8"></script>

            <!-- other js file are loaded inside foooter.php -->
    </head>
