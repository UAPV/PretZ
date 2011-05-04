<?php

/**
  * This filter checks the application configuration to determine which modules/actions are supposed
  * to be secure and ensures they are using https.  This filter will also redirect https
  * requests to non-secured pages back to http if the strict option is set in the configuration
  * file.
  *
  *
  * Adapted from Casey Cambra <casey@tigregroup.com> filter.
  */

class uapvSslRedirectFilter extends sfFilter
{ 
  /**
   * executes the filter.  This filter will determine if a
   * request should be http or https and will redirect as such
   * 
   * @param sfFilterChain $filterChain the current symfony filter chain
   * @return boolean redirect status 
   */
  public function execute( $filterChain )
  {
      //only run once per request
    if( $this->isFirstCall() )
    {
      $request = $this->getContext()->getRequest();
      //only filter is the request is get or head
      if( $request->isMethod( 'get' ) || $request->isMethod( 'head' ) )
      {
        //get the current module and action
        //$module = $request->getParameter( 'module' );
        // $action = $request->getParameter( 'action' );

        $module = $this->getContext()->getModuleName() ;
        $action = $this->getContext()->getActionName() ;

        //get the module settings
        $moduleSettings = sfConfig::get( 'app_uapv_ssl_redirect_secure', false );
        //see if strict settings are on (non secure modules must be http)
        $strict = sfConfig::get( 'app_uapv_ssl_redirect_strict', true );

        //if there are settings for this module
        if( isset( $moduleSettings[$module] ) )
        {
          // We put an LDAP object in the context in order to reuse it later
          $this->getContext ()->set ('ldap', new uapvLdap ());

          //there are actions defined, check if this actions is secure
          if( isset( $moduleSettings[ $module ][ 'actions' ] ) )
          {
            //this is a secure action
            if( !$request->isSecure() &&
                is_array( $moduleSettings[ $module ][ 'actions' ] ) &&
                in_array($action, $moduleSettings[ $module ][ 'actions' ])
            )
              //we need to redirect to a secure url
              return $this->redirectSecure( $request );
          }
          else if( !$request->isSecure() )
            //every action in this module is secure, redirect
            return $this->redirectSecure( $request );
        }
        else if( $request->isSecure() && $strict )
          //redirect back to http, strict is set
          return $this->redirectUnsecure( $request );
      }
    }
    //no redirect necessary, continue the filter chain
    $filterChain->execute();
  }

  /**
   * redirects an http request to https
   * 
   * @param sfWebRequest $request
   * @return boolean
   */
  protected function redirectSecure( $request )
  {
    //replace http w/ https
    $url = str_replace( 'http', 'https', $request->getUri() );
    return $this->getContext()->getController()->redirect( $url, 0, 301 );
  }

  /**
   * redirects an https request to http
   *
   * @param sfWebRequest $request
   * @return boolean
   */
  protected function redirectUnsecure( $request )
  {
    //replace https w/ http
    $url = str_replace( 'https', 'http', $request->getUri() );
    return $this->getContext()->getController()->redirect( $url, 0, 301 );
  }
}
