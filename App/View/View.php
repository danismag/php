<?php

namespace App\View;

/**
*   Класс для отображения страниц
*   
*   'form' = - объект отзыва для отображения в форме
*   'feed' = [] - массив объектов-отзывов
*   'comment' - объект отзыва для просмотра
*   'errors' = [] - массив исключений ['status', 'message']
*   'login' - исключение входа пользователя
*   'image' - объект изображения
*   @property array $data - массив для хранения данных
*/
class View
{   
    // Пути к шаблонам
    
    const TMAIN         = __DIR__ . '/../templates/mainView.php';
    const TLOGINFORM    = __DIR__ . '/../templates/loginFormView.php';
    const TFEEDFORM     = __DIR__ . '/../templates/feedFormView.php';
    const TCOMMENT      = __DIR__ . '/../templates/commentView.php';
    const TPREVIEW      = __DIR__ . '/../templates/commentPreView.php';
    const TALERT        = __DIR__ . '/../templates/alertView.php';
    const TCOMADMIN     = __DIR__ . '/../templates/commentAdminView.php';
    const TCOMEDIT      = __DIR__ . '/../templates/commentEditView.php';
    
    
    // поле для хранения переданных данных
    protected $data = [];
    
    // Обработка присваивания значения несуществующему свойству
    public function __set ($key, $object)
    {
        $this->data[$key] = $object;
    }
    
    // Обработка запроса на чтение недоступного свойства
    public function __get ($key)
    {
        return $this->data[$key];
    }
    
    // Обработка проверки существования недоступного свойства
    public function __isset ($key)
    {
        return array_key_exists($key, $this->data);      
    }
    
    /**
    *   Отображение главной страницы
    *
    *   @param object 'form' - объект отзыва для отображения в форме
    *   @param array 'feed' - массив отзывов
    *   @param array 'errors' - массив сообщений об исключениях
    *   @param array 'login' - сообщение о входе
    */
    public function mainPage()
    {
        // Подготовка данных
        $this->data['loginForm'] = $this->emptyRender(self::TLOGINFORM);
        $this->data['feedForm'] = $this->commentRender('form', self::TFEEDFORM);
        $this->data['content'] = $this->comsRender('feed', self::TCOMMENT);
        $this->data['message'] = $this->alertRender('errors', self::TALERT);
        $this->data['errorLogin'] = $this->alertRender('login', self::TALERT);
        
        // Отображение главного шаблона
        echo $this->render(self::TMAIN);
    }
    
    /**
    *   Предпросмотр отзыва
    *
    *   @param object 'comment' - объект отзыва
    */
    public function preview()
    {
        if (isset($this->data['comment'])) {
            
            if (isset($this->data['image'])) {
                
                $this->data['comment'][] = $this->data['image'];
            }
            
            echo $this->commentRender('comment', self::TPREVIEW);
        }
    }
    
    /**
    *   Получение html-кода массива отзывов
    *
    *   @param array $comments - массив отзывов
    *   @param string $template - путь к шаблону для отзыва
    *   @return string 
    */
    private function comsRender($coms, $template)
    {
        $result = '';
        
        if (isset($this->data[$coms])) {
            
            foreach($this->data[$coms] as $comment) {
            
                $result .= $this->commentRender($comment, $template);                
            }
        }
        
        return $result;        
    }
    
    /**
    *   Внесение данных отзыва в шаблон
    *
    *   @param object $comment - объект отзыва
    *   @param string $template - путь к шаблону
    *   @return string - html-код отзыва
    */
    private function commentRender($com, $template)
    {
        ob_start();
        
        if (isset($this->data[$com])) {
            
            foreach($this->data[$com] as $key => $val) {
                
                $$key = $val;
            }
            
        }
        
        include $template;
        
        return ob_get_clean();        
    }
    
    /**
    *   Внесение данных сообщения в шаблон
    *
    *   @param object $alert - массив отзыва
    *   @param string $template - путь к шаблону
    *   @return string - html-код сообщения
    */
    private function alertRender($alert, $template)
    {
        
        if (isset($this->data[$alert]['message'])) {
            
            ob_start();
            
            foreach($this->data[$alert] as $key => $val) {
                
                $$key = $val;
            }
            
            include $template;
            
            return ob_get_clean();
        }
        
    }
    
    /**
    *   "простое" внесение данных в шаблон
    *
    *   @return string 
    *   @param $template string Путь к файлу-шаблону
    */
    private function render($template)
    {
        ob_start();
        
        // Установка переменных для шаблона
        
        foreach ($this->data as $key => $value) {
            
            $$key = $value;
        }
        
        include $template;
        
        return ob_get_clean();      
        
    }   
    
    /**
    *   только пустой шаблон
    *
    *   @return string 
    *   @param $template string - путь к шаблону
    */
    private function emptyRender($template)
    {
        ob_start();
        
        include $template;
        
        return ob_get_clean();   
    }
}

?>