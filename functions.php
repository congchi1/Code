add_filter('woocommerce_product_categories_widget_args','woo_current_product_category');
function woo_current_product_category( $args ){
    
    global $wp_query, $post, $woocommerce;
    
    $include = array();
    
    $category_parent     = '';
    $current_cat        = '';
    
    if ( is_tax( 'product_cat' ) ) {
        
        $cat_obj = $wp_query->get_queried_object();
        
        if( isset( $cat_obj->term_id ) ){
            
            $current_cat     = $cat_obj;            
            $category_parent = $cat_obj->parent;
        }
        
    } elseif ( is_singular('product') ) {
        
        $product_category     = wc_get_product_terms( $post->ID, 'product_cat', array( 'orderby' => 'parent', 'fields' => 'all' ) );
        $current_cat         = end( $product_category );
        $category_parent     = $current_cat->parent;
                
    }
    
    //check if current cat has children
    if( ! empty( $current_cat ) )
        $current_cat_children = 
            get_terms( 
                'product_cat', 
                array( 
                    'parent' => $current_cat->term_id, 
                    'fields' => 'ids', 
                    'hide_empty' => 0 
                ) 
            );
    
    if( ! empty( $current_cat_children ) ){
        
        $terms_to_include = 
            array_merge( 
                array( $current_cat->term_id ), 
                $current_cat_children 
            );
        
    }else{
        
        if( ! empty( $category_parent ) ){
            $terms_to_include = 
                array_merge( 
                    array( $category_parent ), 
                    get_terms( 'product_cat', array( 'parent' => $category_parent, 'fields' => 'ids', 'hide_empty' => 0 ) )
                 );
        }
            
    }
    
    if( ! empty( $terms_to_include ) )
        $include = $terms_to_include;
        
    if( ! empty( $include ) )
        $args['include'] = $include;
    
    return $args;
 
}
