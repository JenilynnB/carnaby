<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


get_header();
?>
<section class="primary homepage">
    
    <div class="section-fullwidth main-panel" style="background-image: 
         url(<?php echo site_url('/wp-content/uploads/Carnaby-Homepage1.jpg');?>);">
        <div class="container">
            <div class="row">
                <div class="column-md-12">
                    <div>
                        <h1>Discover the best places to shop online</h1>
                        <div class="search-box">
                            <?php
                            if( isset($smof_data['search_box']) && $smof_data['search_box'] == 1):
                                get_search_form();    
                            endif; 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<?php
get_footer();


?>