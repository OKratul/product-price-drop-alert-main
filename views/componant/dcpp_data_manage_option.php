<?php 


    class dcpp_data_manage_option{

     
        public function manage_option_modal($id, $product_name,$product_price,$product_id){

            ?>
                <div class="modal fade" id="manage-option-modal-<?php echo $id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo $product_name ?></h5>
                                <br>
                                <p><?php echo $product_price ?> tk</p>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <?php $this->data_manage_form($id,$product_id) ?>
                            </div>
                        
                        </div>
                    </div>
                </div>
            <?php


        }


        public function data_manage_form($id,$product_id){
            ?>
            
                <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="manage_option_form">
                    <input type="hidden" name="action" value="dcpp_manage_option">
                    <input type="hidden" name="notification_id" value="<?php echo esc_attr($id); ?>">
                    <input type="hidden" name="product_id" value="<?php echo esc_attr($product_id); ?>">
            
                    <?php 
                    // Nonce field for security
                    wp_nonce_field('dcpp_manage_option_action', 'dcpp_manage_option_nonce'); 
                    ?>
            
                    <div class="manage-form">
                        <label>Choose Your Action</label>
                        <select style="width:100%" class="form-select manage-option-select" name="manage_option" id="manage-select-option-<?php echo $id; ?>" aria-label="Select action">
                            <option selected disabled>Open this select menu</option>
                            <option value="Send Email When I Drop Price">Send Email When I Drop Price</option>
                            <option value="Send Email With Discount Coupon Code">Send Email With Discount Coupon Code</option>
                            <option value="Quarantine">Quarantine</option>
                            <option value="Remove">Remove</option>
                        </select>
                    </div>
                    <hr>
                    <div id="" class="coupon-fields" style="display: none;">
                        <div class="form-group">
                            <div class="mb-2">
                                <label for="discount-type">Discount Type</label>
                                <select id="discount_type" name="discount_type" class="select short">
                                    <option value="percent">Percentage discount</option>
                                    <option value="fixed_cart" selected="selected">Fixed cart discount</option>
                                    <option value="fixed_product">Fixed product discount</option>     
                                </select>
                            </div>
                            <div class="row mb-2">
                                <div class="form-field coupon_amount_field col-6">
                                    <label for="coupon_amount">Coupon amount</label>
                                    <input type="text" class="short wc_input_price" name="coupon_amount" id="coupon_amount" value="0" placeholder="0"> 
                                </div>
            
                                <div class="form-field expiry_date_field col-6">
                                    <label for="expiry_date">Coupon expiry date</label>
                                    <input type="date" class="date-picker" name="expiry_date" id="expiry_date" value="" placeholder="YYYY-MM-DD">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label>Allow for free Shipping</label>
                                <input value="1" type="checkbox" name="allow_free_shipping" class="">
                            </div>
                            <div class="mb-2">
                                <label>Allow only for this email address</label>
                                <input value="1" type="checkbox" name="allow_specific_email" class="">
                            </div>
                        </div>
                    </div>
            
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                </form>
            
            <?php 
            }
            

     

    }

    new dcpp_data_manage_option();