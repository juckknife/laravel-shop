<?php

namespace App\Admin\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\AttrKey;
use App\Models\AttrVal;
use App\Models\Category;
use App\Models\Product;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class ProductsController extends Controller
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
            $content->header(trans('admin.product'));
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
            $content->header('编辑商品');
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
            $content->header('创建商品');
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
        return Admin::grid(Product::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->image('商品主图')->image('image', 80, 80);
            $grid->title('商品名称');
            $grid->column('category.title', '类目');
            $grid->on_sale('已上架')->display(function ($value) {
                return $value ? '是' : '否';
            });
            $grid->agent_price('批发价');
            $grid->price('价格');
            $grid->rating('评分');
            $grid->sold_count('销量');
            $grid->review_count('评论数');

            $grid->actions(function ($actions) {
                $actions->disableView();
                $actions->disableDelete();
            });
            $grid->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
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
        return Admin::form(Product::class, function (Form $form) {
            // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
            $form->text('title', '商品名称')->rules('required');
            // 创建一个下拉框
            $form->select('category_id', '类目')->options(Category::selectOptions())
                ->load('attr_symbol_path', '/api/attrs/categoryvals');
            // 创建一个选择图片的框
            $form->image('image', '封面图片')->rules('required|image');
            // 创建一个富文本编辑器
            $form->editor('description', '商品描述')->rules('required');
            // 创建一组单选框
            $form->radio('on_sale', '上架')->options(['1' => '是', '0' => '否'])->default('0');
            // 创建轮播图
            $form->multipleImage('detail.imgs', '轮播图')->removable();
            // 直接添加一对多的关联模型
            $form->hasMany('skus', '商品SKU', function (Form\NestedForm $form) {
                $form->text('title', 'SKU 名称')->rules('required');
                $form->text('description', 'SKU 描述')->rules('required');
                $form->text('price', '单价')->rules('required|numeric|min:0.01');
                $form->text('stock', '剩余库存')->rules('required|integer|min:0');
                $form->multipleSelect('attr_symbol_path', '属性值')->options(function (){
                    return AttrVal::query()->get()->pluck('value', 'id');
                });

            });
            // 定义事件回调，当模型即将保存时会触发这个回调
            $form->saving(function (Form $form) {
                foreach ($form->skus as $sku){
                    $existed = [];
                    foreach ($sku['attr_symbol_path'] as $valId) {
                        if (!$valId) continue;
                        $key = AttrVal::find($valId);
                        if (isset($existed[$key->attr_key_id])){
                            $error = new MessageBag([
                                'title'   => '参数错误',
                                'message' => '同类商品属性选择了多个,请调整!',
                            ]);

                            return back()->with(compact('error'));
                        }
                        $existed[$key->attr_key_id] = $valId;
                    }
                }
                $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
            });
        });
    }
}
