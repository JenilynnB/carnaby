
<?php
global $post;


$listing_id = $post->ID;
$title = $post->post_title;

$module = "";

if(isset($_GET["module"])){	$module = $_GET["module"];	}

if($module == "" || $module == "reviews"):

?>

<?php if($module == "reviews"): ?>
<section class="primary-section reviews section">
<?php else: ?>
<section class="primary-section reviews">    
<?php endif; ?>
    
    <div class="container">
        <div class="content">
            
            <?php global $xoouserultra;   ?>
            <div class="row">
                
                    <?php
                    if($module == "reviews"):
                    ?>    
                    <div class="col-md-12 col-sm-12 col-xs-12">  
                        <a href="?" class="breadcrumb">< Back to <?php echo $title;?> listing</a>
                    <?php
                    else:
                    ?>
                    
                    <div class="col-md-8">
                        
                    <?php
                    endif;
                    ?>  

                    <?php if($num_reviews>0):?>
                        <h2><?php echo $title; ?> Reviews (<?php echo $num_reviews;?>)</h2>
                    <?php else: ?>
                        <h2><?php echo $title; ?> Reviews </h2>
                    <?php endif;?>   
                    
                    <div class="col-md-12">
                        <div class="row">
                            <div class="listing-ratings">
                                <?php if ($ratings): ?>
                                    <div class="row">
                                        <?php if ($success): ?>
                                            <div class="alert alert-success">
                                                <?php if (wpbdp_get_option('ratings-require-approval')): ?>
                                                    <?php _e('Your review has been saved and is waiting for approval.', 'wpbdp-ratings'); ?>
                                                <?php else: ?>
                                                    <?php _e('Your review has been published!', 'wpbdp-ratings'); ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>

                                            <div class="message">
                                                <?php if ($reason == 'already-rated'): ?>
                                                    <?php _e('(You have already rated this listing)', 'wpbdp-ratings'); ?>


                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>


                                    <!--Loop for each review-->
                                    <?php foreach ($ratings as $i => $rating): 

                                        $vars=array();
                                        $vars['rating'] = $rating;
                                        echo wpbdp_render(WPBDP_RATINGS_TEMPLATES_PATH.'/single-review.tpl.php', $vars);
                                        ?>

                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php
                            
                            $total_pages = ceil($num_reviews/NUM_REVIEWS_TO_PAGINTE);
                            if($module==reviews){
                                $page_num = 2;
                            }else{
                                $page_num = 1;
                            }
                            if($num_reviews > NUM_REVIEWS_TO_PAGINTE):
                            ?>
                                <div class="col-md-8 col-md-8 hidden-sm hidden-xs load-more-reviews">
                                    <a href="" class="btn btn-secondary more-info more-reviews" 
                                       data-page_num="<?php echo $page_num;?>" 
                                       data-page_max="<?php echo $total_pages;?>" 
                                       data-listing_id="<?php echo $listing_id;?>">More Reviews...</a>
                                </div>

                                <?php /**Add mobile button here for loading new page and getting next 5 reviews*/  ?>
                                <div class="hidden-lg hidden-md col-sm-12 col-xs-12">
                                    <a href="?module=reviews" class="btn btn-secondary more-info">More Reviews...</a>
                                </div>
                            <?php endif; ?>
                              
                        </div>



                            <div class="row">
                                <!--Message for no reviews-->
                                <p class="no-reviews-message" style="<?php echo $ratings ? 'display: none;' : ''?>"><?php _e('There are no reviews yet. Be the first!', 'wpbdp-ratings'); ?></p>

                                <?php if (!has_written_review() || !is_user_logged_in()) : ?>
                                    <?php echo $review_form; ?>
                                <?php else: ?>

                                <?php endif; ?>    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade modal-small" id="confirm-review-delete" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-body">
        <p>Are you sure you want to delete this review?</p>
        <button type="button" class="btn btn-default" data-dismiss="modal">Woah, no way!</button>
        <button type="button" class="btn btn-primary confirm-delete" data-dismiss="modal">Yep, trash it</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
endif;
?>




<?php
if($module != "more_info" && $module != "reviews"):

?>

<section class="primary section">
    <div class="container">
        <div class="content">                
            <div class="row">
                <div class="col-md-12">
                    <?php the_related($listing_id); ?>
                                
                </div>
            </div>
        </div>
    </div>
</section>

<?php endif; ?>