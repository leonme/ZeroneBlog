<?php
/*
Template Name:留言板
*/
?>
<?php get_header(); ?>
<?php get_header('masthead'); ?>

    <?php comments_template('/guestComments.php');?>
<?php get_footer('colophon'); ?>
<?php get_footer(); ?>