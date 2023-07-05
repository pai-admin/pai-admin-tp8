<?php
declare (strict_types = 1);

namespace app;

use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    protected $method='';

    /**
     * @var null
     * 当前模块
     */
    protected $module = '';

    /**
     * @var null
     * 当前控制器
     */
    protected $controller = '';

    /**
     * @var string
     * 当前操作
     */
    protected $action = '';

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        $this->method = $this->request->method();

        $this->module = app('http')->getName();
        $this->controller = $this->request->controller();
        $this->action = $this->request->action();

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {}
}
