<?php
namespace Wal\Router;

// Import des routes
require dirname(__DIR__, 2) . '/config/routes.php';

/*
|--------------------------------------------------------------------------
|   Classe Router
|--------------------------------------------------------------------------
|   Le routeur doit connaître 3 informations, définies dans des attributs :
|
|   - $routes : l’ensemble des routes de l’application (définies dans 📄 config/routes.php)
|   - $availablePaths : l’ensemble des chemins contenus dans ces routes (Exemple : /, /mentions-legales…)
|   - $requestedPath : le chemin demandé par le client
|
*/
class Router {
    
    private $routes;
	private $availablePaths;
	private $requestedPath;

    /**
     * Le constructeur de la classe Router initialise les attributs $routes, $availablePaths et $requestedPath.
     * Il appelle ensuite la méthode parseRoutes().
     */
    public function __construct() {
		$this->routes = ROUTES;
		$this->availablePaths = array_keys($this->routes);
		$this->requestedPath = isset($_GET['path']) ? $_GET['path'] : '/';
		$this->parseRoutes();
	}

    /**
     * La méthode explodePath() permet de :
     * - Supprimer l’éventuel premier et dernier / présent dans le chemin
     * - D’éclater $_GET['path'] sur le caractère /
     * 
     * @param string $path
     * @return array
     */
    private function explodePath(string $path): array {
		return explode('/', rtrim(ltrim($path, '/'), '/'));
	}

    /**
     * La méthode isParam() permet de vérifier si un morceau de chemin est un paramètre.
     * 
     * @param string $candidatePathPart
     * @return bool
     */
    private function isParam(string $candidatePathPart): bool {
		return str_contains($candidatePathPart, '{') && str_contains($candidatePathPart, '}');
	}

    /**
     * Parsage des routes
     * @return void
     */
    private function parseRoutes(): void {
		$explodedRequestedPath = $this->explodePath($this->requestedPath);
        $params = [];

        foreach ($this->availablePaths as $candidatePath) {
            $foundMatch = true;
            $explodedCandidatePath = $this->explodePath($candidatePath);

            if (count($explodedCandidatePath) == count($explodedRequestedPath)) {

                // Boucle sur chaque élément du chemin demandé par le client
				foreach ($explodedRequestedPath as $key => $requestedPathPart) {
                    
                    $candidatePathPart = $explodedCandidatePath[$key];

                    // Si l'élément du chemin est un paramètre, il faut le récupérer.
					if ($this->isParam($candidatePathPart)) {
						$params[substr($candidatePathPart, 1, -1)] = $requestedPathPart;
                    // Sinon le chemin candidat ne possède pas de paramètre mais bien une portion statique, 
                    // il faut la comparer à la portion correspondante du chemin demandé par le client.
					}else if ($candidatePathPart !== $requestedPathPart) {
						$foundMatch = false;
						break;
					}

				}
                // Si une correspondance est trouvée, on stocke la route et on sort de la boucle
                if ($foundMatch) {
					$route = $this->routes[$candidatePath];
					break;
				}

			}
		}

        // Si une route a été trouvée, on instancie le contrôleur et on appelle la méthode associée
        if (isset($route)) {
			$controller = new $route['controller'];
			$controller->{$route['method']}(...$params);
		}
	}
    
}