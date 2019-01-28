<?php

class Shopware_Controllers_Frontend_ShareBasket extends Enlight_Controller_Action
{
    public function indexAction()
    {
    }

    public function loadAction()
    {
        /** @var sBasket $basketModule */
        $basketModule = $this->container->get('modules')->Basket();
        $basketModule->sDeleteBasket();

        $request = $this->Request();

        $basket = $this->getBasket($request->getParam('bID'));

        $articles = json_decode($basket->articles);

        foreach ($articles as $article) {
            if ($article->modus === 1) {
                $this->container->get('system')->_GET['sAddPremium'] = $article->ordernumber;
                $basketModule->sInsertPremium();
            } elseif ($article->modus == 2) {
                $basketModule->sAddVoucher($article->ordernumber);
            } else {
                $this->container->get('events')->notify('SpShareBasket_Controller_loadAction_addArticke_Start', ['article' => $article]);
                $insertId = $basketModule->sAddArticle($article->ordernumber, $article->quantity ?: 1);
                $insertId = $this->container->get('events')->filter('SpShareBasket_Controller_loadAction_addArticke_Added', $insertId);
                $this->updateBasketMode($article->modus, $insertId);
            }

            foreach ($article->attributes as $attribute => $value) {
                if ($value !== null) {
                    $this->updateBasketPosition($insertId, $attribute, $value);
                }
            }
        }

        $this->redirect(
            ['controller' => 'checkout']
        );
    }

    public function saveAction()
    {
        $attributesToStore = $this->container->get('config')->getByNamespace('SpShareBasket', 'attributesToStore');

        /** @var sBasket $basketModule */
        $basketModule = $this->container->get('modules')->Basket();

        $BasketData = $basketModule->sGetBasketData();

        $articles = [];
        foreach ($BasketData['content'] as $key => $article) {
            if ($article['modus'] == 2) {
                $voucher = $basketModule->sGetVoucher();
                $article['ordernumber'] = $voucher['code'];
            }

            $basketArticle = [
                'ordernumber' => $article['ordernumber'],
                'quantity' => $article['quantity'],
                'modus' => $article['modus'],
            ];

            foreach ($this->getBasketAttributes($article['id']) as $attribute => $value) {
                if ($attribute !== 'id' && $attribute !== 'basketID' && $value !== null && in_array($attribute, $attributesToStore)) {
                    $basketArticle['attributes'][$attribute] = $value;
                }
            }

            $articles[] = $basketArticle;
        }

        $basketID = $this->saveBasket(json_encode($articles));

        $this->redirect(
            [
                'module' => 'frontend',
                'action' => 'cart',
                'controller' => 'checkout',
                'bID' => $basketID,
            ]
        );
    }

    public function getBasket($basketID)
    {
        /** @var \Doctrine\DBAL\Query\QueryBuilder $builder */
        $builder = $this->container->get('dbal_connection')->createQueryBuilder();
        $builder->select('*')
            ->from('s_plugin_sharebasket_baskets')
            ->where('basketID = :basketID')
            ->setParameter(':basketID', $basketID);

        $statement = $builder->execute();

        return $statement->fetch(\PDO::FETCH_OBJ);
    }

    public function saveBasket($articles)
    {
        $basketID = uniqid('SpSB', true);
        $this->container->get('dbal_connection')->insert('s_plugin_sharebasket_baskets', [
            'basketID' => $basketID,
            'articles' => $articles,
            'session_id' => $this->container->get('session')->get('sessionId'),
            'time' => date('Y-m-d H:i:s'),
        ]);

        return $basketID;
    }

    public function getBasketAttributes($basketID)
    {
        /** @var \Doctrine\DBAL\Query\QueryBuilder $builder */
        $builder = $this->container->get('dbal_connection')->createQueryBuilder();
        $builder->select('soba.*')
            ->from('s_order_basket', 'sob')
            ->innerJoin('sob', 's_order_basket_attributes', 'soba', 'soba.basketID = sob.id')
            ->where('sob.id = :basketID')
            ->andWhere('sessionID = :sessionID')
            ->setParameters([
                ':basketID' => $basketID,
                ':sessionID' => $this->container->get('session')->get('sessionId'),
            ]);
        $statement = $builder->execute();

        return $statement->fetch();
    }

    public function updateBasketPosition($basketID, $field, $value)
    {
        $sql = 'UPDATE
			s_order_basket sob
		INNER JOIN s_order_basket_attributes soba ON (
			soba.basketID = sob.id
		)
		SET
			soba.' . $field . ' = ?
		WHERE
			sob.id = ?
			AND sessionID = ?';

        try {
            $this->container->get('db')->query(
                $sql,
                [
                    $value,
                    $basketID,
                    $this->container->get('session')->get('sessionId'),
                ]
            );
        } catch (Exception $e) {
        }
    }

    public function updateBasketMode($modus, $basketID)
    {
        /** @var \Doctrine\DBAL\Query\QueryBuilder $builder */
        $builder = $this->container->get('dbal_connection')->createQueryBuilder();
        $builder->update('s_order_basket')
            ->set('modus', $modus)
            ->where('id = :basketID')
            ->andWhere('sessionID = :sessionID')
            ->setParameters([
                ':basketID' => $basketID,
                ':sessionID' => $this->container->get('session')->get('sessionId'),
            ])
            ->execute();
    }
}
