<?php
namespace Wal\Controller;

abstract class AbstractController {

    /**
     * Le rôle de la fonction renderView() est de retourner le template d'une page HTML.
     * 
     * @param string $template
     * @param array $data
     * @return string
     */
    protected function renderView(string $template, array $data = []): string {
		  $templatePath = dirname(__DIR__, 2) . '/templates/' . $template;
		  return require_once dirname(__DIR__, 2) . '/templates/layout.php';
	  }

    /**
     * La fonction redirectToRoute() effectue les redirections.
     * 
     * @param string $path
     * @param array $params
     * @return void     
     */
    protected function redirectToRoute(string $path, array $params = []): void {
		  $uri = $_SERVER['SCRIPT_NAME'] . "?path=" . $path;

      if (!empty($params)) {
        $strParams = [];
        foreach ($params as $key => $val) {
          array_push($strParams, urlencode((string) $key) . '=' . urlencode((string) $val));
        }
        $uri .= '&' . implode('&', $strParams);
      }
      var_dump($uri); die();
      header("Location: " . $uri);
      die;
	}

}