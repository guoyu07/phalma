# Phalcon最佳实践

## Debugger功能
## Sword模板引擎支持
Sword是一种类似于Laravel的Blade的引擎

##事件全局监听
Phalcon默认不支持全局监听功能的，每个实例的的事件监听需要分别处理，这样不利用功能的解耦.
Phalma实现了此功能。
```php
    <?php
    
      $this->eventsManager->peekEvents(function ($source,$data,$event){
               echo $event;
            });
```
##多模块统一处理

##Widget功能

##Model功能扩展

##modelsManager功能扩展

##dispatcher的改进