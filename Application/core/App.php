<?php

namespace Application\core;


/**
* Esta classe é responsável por obter da URL o controller, método (ação) e os parâmetros
* e verificar a existência dos mesmo.
*/
class App
{
  protected $controller = 'Home';
  protected $method = 'index';
  protected $page404 = false;
  protected $params = [];

  // Método construtor
  public function __construct()
  {
    $URL_ARRAY = $this->parseUrl();
    $this->getControllerFromUrl($URL_ARRAY);
    $this->getMethodFromUrl($URL_ARRAY);
    $this->getParamsFromUrl($URL_ARRAY);

    // chama um método de uma classe passando os parâmetros
    call_user_func_array([$this->controller, $this->method], $this->params);
  }

  /**
  * Este método pega as informações da URL (após o dominio do site) e retorna esses dados
  *
  * @return array
  */
  private function parseUrl()
  {
    $REQUEST_URI = explode('/', substr(filter_input(INPUT_SERVER, 'REQUEST_URI'), 1));
    return $REQUEST_URI;
  }

  /**
  * Este método verifica se o array informado possui dados na psoição 0 (controlador)
  * caso exista, verifica se existe um arquivo com aquele nome no diretório Application/controllers
  * e instancia um objeto contido no arquivo, caso contrário a variável $page404 recebe true.
  *
  * @param  array  $url   Array contendo informações ou não do controlador, método e parâmetros
  */
  private function getControllerFromUrl($url)
  {
    if ( !empty($url[0]) && isset($url[0]) ) {
      if ( file_exists('../Application/controllers/' . ucfirst($url[0])  . '.php') ) {
        $this->controller = ucfirst($url[0]);
      } else {
        $this->page404 = true;
      }
    }

    require '../Application/controllers/' . $this->controller . '.php';
    $this->controller = new $this->controller();

  }

  /**
  * Este método verifica se o array informado possui dados na psoição 1 (método)
  * caso exista, verifica se o método existe naquele determinado controlador
  * e atribui a variável $method da classe.
  *
  * @param  array  $url   Array contendo informações ou não do controlador, método e parâmetros
  */
  private function getMethodFromUrl($url)
  {
    if ( !empty($url[1]) && isset($url[1]) ) {
      if ( method_exists($this->controller, $url[1]) && !$this->page404) {
        $this->method = $url[1];
      } else {
        // caso a classe ou o método informado não exista, o método pageNotFound
        // do Controller é chamado.
        $this->method = 'pageNotFound';
      }
    }
  }

  /**
  * Este método verifica se o array informador possui a quantidade de elementos maior que 2
  * ($url[0] é o controller e $url[1] o método/ação a executar), caso seja, é atrbuido
  * a variável $params da classe um novo array  apartir da posição 2 do $url
  *
  * @param  array  $url   Array contendo informações ou não do controlador, método e parâmetros
  */
  private function getParamsFromUrl($url)
  {
    if (count($url) > 2) {
      $this->params = array_slice($url, 2);
    }
  }
}
