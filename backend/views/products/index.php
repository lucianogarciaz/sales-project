<?php


use yii\helpers\Url;
use yii\helpers\Html;

$this->title = $title;
$this->params['breadcrumbs'] = [
    $this->title];
?>
<div class="row">
    <div class="col-md-12">
        <button type="button" class="btn btn-primary pull-right" data-modal="<?= Url::to(['/products/new']) ?>"
                data-hint="New Product">
            <i class="fa fa-plus"></i> New Product
        </button> 
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box">         
            <?php if (count($products) == 0) : ?>
                <p><strong>Doesn't exists products</strong></p>
            <?php else: ?>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-responsive">
                            <thead>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Actions</th>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>                                        
                                        <td><?= Html::encode($product['Product']) ?></td>
                                        <td><?= Html::encode($product['Description']) ?></td>
                                        <td><?= Html::encode($product['Price']) ?></td>
                                        <td>
                                            <button type="button" class="btn btn-default"
                                                                data-ajax="<?= Url::to(['/products/disable', 'IdProduct' => $product['IdProduct']]) ?>" 
                                                                data-hint="Delete"
                                                                data-mensaje="Are your sure?">
                                                            <i class="fa fa-trash fa-lg"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        <?php endif; ?>
    </div>
</div>

