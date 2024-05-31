<?php
namespace App\Manager;

use Wal\Manager\ModelAbstractManager;
use App\Model\Article;
use PDOStatement;

class ArticleManager extends ModelAbstractManager {

    /**
     *  La méthode find() récupère une ressource spécifique selon son identifiant. 
     *  Elle exploite en arrière-plan la méthode parente readOne().
     *  Article::class est automatiquement traduit par la chaîne de caractère App\Model\Article
     *  Article sera traduit en article (nom dela table)
     * 
     * @param int $id 
     * @return mixed
     */
    public function find(int $id) {
		return $this->readOne(Article::class, [ 'id' => $id ]);
	}

    /**
     *  La méthode findOneBy() récupère une ressource spécifique en fonction de un ou plusieurs critères. 
     *  Elle exploite en arrière-plan la méthode parente readOne().
     *  Article::class est automatiquement traduit par la chaîne de caractère App\Model\Article 
     *  Article sera traduit en article (nom dela table)
     * 
     * @param array $filters 
     * @return mixed    
     */
    public function findOneBy(array $filters) {
		return $this->readOne(Article::class, $filters);
	}

    /**
     * la méthode findAll() récupère toutes les ressources. 
     * Elle exploite en arrière plan la méthode parente readMany().
     * 
     * @return mixed      
     */
    public function findAll() {
		return $this->readMany(Article::class);
	}

    /**
     * la méthode findBy()récupère toutes les ressources répondant à un ou plusieurs critères, de les ordonner, limiter leur nombre et 
     * décaler le curseur de sélection. 
     * Elle exploite en arrière-plan la méthode parente readMany()
     * 
     * @param array $filters 
     * @param array $order 
     * @param int|null $limit 
     * @param int|null $offset 
     * @return mixed      
     */
    public function findBy(array $filters, array $order = [], int $limit = null, int $offset = null) {
		return $this->readMany(Article::class, $filters, $order, $limit, $offset);
	}
    
    /**
     * La méthode add() insère une ressource pour un Model donné
     * Elle exploite en arrière-plan la méthode parente create()
     * 
     * @param Article $article 
     * @return PDOStatement  
     */
    public function add(Article $article) {
		return $this->create(Article::class, [
				'title' => $article->getTitle(),
				'description' => $article->getDescription(),
				'content' => $article->getContent()
			]
		);
	}


    /**
     * La méthode edit() met à jour une ressource pour un Model donné
     * Elle exploite en arrière-plan la méthode parente update()
     * 
     * @param Article $article 
     * @return PDOStatement      
     */
    public function edit(Article $article) {
		return $this->update(Article::class, [
				'title' => $article->getTitle(),
				'description' => $article->getDescription(),
				'content' => $article->getContent()
			],
			$article->getId()
		);
	}

    /**
     * La méthode delete() supprime une ressource pour un Model donné
     * Elle exploite en arrière-plan la méthode parente remove()
     * 
     * @param Article $article 
     * @return PDOStatement      
     */
    public function delete(Article $article) {
		return $this->remove(Article::class, $article->getId());
	}
}