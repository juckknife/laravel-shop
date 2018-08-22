<?php

namespace App\Admin\Controllers;

use App\Models\AttrKey;
use App\Models\Category;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class AttrsController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('admin.attrs'));
            $content->description(trans('admin.list'));
            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header(trans('admin.attrs'));
            $content->description(trans('admin.edit'));
            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('admin.attrs'));
            $content->description(trans('admin.create'));
            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(AttrKey::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->name(trans('admin.attrs').trans('admin.name'));
            $grid->values('属性值')->pluck('value')->label();
            $grid->column('category.title', '类目');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        // 创建一个表单
        return Admin::form(AttrKey::class, function (Form $form) {
            // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
            $form->text('name', trans('admin.attrs').trans('admin.name'))->rules('required');
            // 创建一个下拉框
            $form->select('category_id', '类目')->options(Category::selectOptions());
            // 直接添加一对多的关联模型
            $form->hasMany('values', '属性值', function (Form\NestedForm $form) {
                $form->text('value', '属性值')->rules('required');
            });
        });
    }
}
