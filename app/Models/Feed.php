<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use App\Models\Aggregators;
use App\Models\CategoryDescription;
use XMLWriter;

class Feed extends Aggregators
{
    private $xml;
    private $price_marks;
    private $doc;
    private $company;
    private $type;
    private $products;
    private $categories;

    public function __construct()
    {
        parent::__construct();

        $this->price_marks = [0 => 'rrc_price', 1 => 'base_price', 2 => 'purchase_price'];
        $this->company = "Palladium"; //сделать в админке

        $this->xml = new XMLWriter();
        $this->xml->openMemory();
        $this->xml->setIndent(1);
        $this->xml->startDocument("1.0", "UTF-8");
    }

    // Функции генерации

    public function generate($type)
    {
        $this->type = $type;

        $this->products =  $this->getProducts();
        $this->categories = $this->getCategories($this->products);

        $template = $this->getAggregator()->template;
        $data = $template ? $this->$template() : $this->$type();
        return $this->generateXML($data);
    }

    private function getAggregator()
    {
        $aggregator = self::where('slug', $this->type)->first();
        return $aggregator;
    }

    private function generateXML($data)
    {
        $fileName = 'feeds/' . $this->type . '.xml';
        file_put_contents($fileName,$data);
        return redirect($fileName);
    }

    // Шаблоны

    private function hotline()
    {
        $xml = $this->xml;

        $xml->startElement("price");
            $xml->writeElement("date",date("Y-m-d h:i"));
            $xml->writeElement("firmName",$this->company);
            $xml->startElement("categories");
                $this->hotlineCategoriesTemplate();
            $xml->endElement();

            $xml->startElement('items');
                $this->hotlineProductsTemplate('item');
            $xml->endElement();
        $xml->endElement();
        $this->doc .= $xml->outputMemory();
        return $this->doc;
    }

    private function price()
    {
        $xml = $this->xml;

        $xml->startElement('price');
            $xml->writeAttribute("date",date("Y-m-d h:i"));
            $xml->writeElement("name",$this->company);
            $xml->startElement("currency");
				$xml->writeAttribute("id", "UAH");
				$xml->writeAttribute("rate", "1.00");
            $xml->endElement();
            $xml->startElement("catalog");
                $this->priceCategoriesTemplate();
            $xml->endElement();

            $xml->startElement('items');
                $this->priceProductsTemplate('item');
            $xml->endElement();
        $xml->endElement();
        $this->doc .= $xml->outputMemory();
        return $this->doc;
    }

    private function nadavi()
    {
        $xml = $this->xml;
        $xml->startElement("yml_catalog");
            $xml->writeAttribute("date",date("Y-m-d h:i"));
            $xml->startElement("shop");
                $xml->writeElement("name",$this->company);
                $xml->writeElement("url", url('/'));
                $xml->startElement("currencies");
                    $xml->startElement("currency");
                        $xml->writeAttribute("id", "UAH");
                        $xml->writeAttribute("rate", "1.00");
                    $xml->endElement();
                $xml->endElement();
                $xml->startElement("catalog");
                    $this->priceCategoriesTemplate();
                $xml->endElement();
                $xml->startElement('items');
                    $this->priceProductsTemplate('item');
                $xml->endElement();
            $xml->endElement();
        $xml->endElement();
        $this->doc .= $xml->outputMemory();
        return $this->doc;
    }

    private function yandex()
    {
        $xml = $this->xml;
        $xml->startElement("yml_catalog");
            $xml->writeAttribute("date",date("Y-m-d h:i"));
            $xml->startElement("shop");
                $xml->writeElement('name',$this->company);
                $xml->writeElement('company',$this->company);
                $this->currenciesTemplate();
                $xml->startElement('categories');
                    $this->priceCategoriesTemplate();
                $xml->endElement();
                $xml->startElement('offers');
                    $this->yandexProductsTemplate('offer');
                $xml->endElement();
                $this->doc .= $xml->outputMemory();

            $xml->endElement();
        $xml->endElement();
        $this->doc .= $xml->outputMemory();
        return $this->doc;
    }

    private function yandex_short()
    {
        $xml = $this->xml;
        $xml->startElement("yml_catalog");
            $xml->writeAttribute("date",date("Y-m-d h:i"));
            $xml->startElement("shop");
                $xml->writeElement('name',$this->company);
                $xml->writeElement('company',$this->company);
                $xml->startElement('categories');
                    $this->priceCategoriesTemplate();
                $xml->endElement();
                $xml->startElement('offers');
                    $this->yandexShortProductsTemplate('offer');
                $xml->endElement();
            $xml->endElement();
        $xml->endElement();
        $this->doc .= $xml->outputMemory();
        return $this->doc;
    }

    private function yandex_description()
    {
        $xml = $this->xml;
        $xml->startElement("yml_catalog");
            $xml->writeAttribute("date",date("Y-m-d h:i"));
            $xml->startElement("shop");
                $xml->writeElement('name',$this->company);
                $xml->writeElement('company',$this->company);
                $this->currenciesTemplate();
                $xml->startElement('categories');
                    $this->priceCategoriesTemplate();
                $xml->endElement();
                $xml->startElement('offers');
                    $this->yandexDescriptionProductsTemplate('offer');
                $xml->endElement();
                $this->doc .= $xml->outputMemory();

            $xml->endElement();
        $xml->endElement();
        $this->doc .= $xml->outputMemory();
        return $this->doc;
    }

    private function google_pokupki()
    {
        $xml = $this->xml;
        $xml->startElement("rss");
            $xml->writeAttribute("xmlns:g","http://base.google.com/ns/1.0");
            $xml->writeAttribute("version","2.0");
            $xml->startElement("channel");
                $xml->writeElement("title",$this->company);
                $xml->writeElement("link",url('/'));
                $this->googleProductsTemplate('item');
            $xml->endElement();
        $xml->endElement();
        $this->doc .= $xml->outputMemory();
        return $this->doc;
    }

    private function facebook()
    {
        $xml = $this->xml;
        $xml->startElement("feed");
            $xml->writeAttribute("xmlns","http://www.w3.org/2005/Atom");
            $xml->writeAttribute("xmlns:g","http://base.google.com/ns/1.0");
            $xml->writeElement("title",$this->company);
            $xml->startElement("link");
                $xml->writeAttribute("rel","self");
                $xml->writeAttribute("href",url('/'));
            $xml->endElement();
            $this->facebookProductsTemplate('entry');
        $xml->endElement();
        $this->doc .= $xml->outputMemory();
        return $this->doc;
    }

    // Шаблонные функции

    private function facebookProductsTemplate($name)
    {
        $xml = $this->xml;
        foreach ($this->products as $product) {
            $xml->startElement($name);
                $xml->writeElement("g:id", $product->id);
                $xml->writeElement("g:title",$product->title);
                $xml->writeElement("g:description",strip_tags($product->description));
                $xml->writeElement("g:link", url($product->slug));
                $xml->writeElement("g:image_link", url($product->product->image));
                foreach($product->photos as $photos)
                {
                    $xml->writeElement("g:image_link", url($product->product->image));
                }
                $xml->writeElement("g:condition", "new");
                $xml->writeElement("g:availability", "in stock");
                $xml->writeElement("g:price", $this->getPrice($product) . " UAH");
                $xml->writeElement("g:brand",$product->manufacturer->title);
            $xml->endElement();
            $this->doc .= $xml->outputMemory();
        }
    }

    private function googleProductsTemplate($name)
    {

        $xml = $this->xml;
        foreach ($this->products as $product) {
            $xml->startElement($name);
                $xml->writeElement("g:id", $product->id);
                $xml->startElement("g:title");
                    $xml->writeCData($product->title);
                $xml->endElement();
                $xml->startElement("g:description");
                    $xml->writeCData(strip_tags($product->description));
                $xml->endElement();
                $xml->writeElement("g:link", url($product->slug));
                $xml->writeElement("g:adwords_redirect", url($product->slug));
                $xml->writeElement("g:image_link", url($product->product->image));
                foreach($product->photos as $photos)
                {
                    $xml->writeElement("g:image_link", url($product->product->image));
                }
                $xml->writeElement("g:condition", "new");
                $xml->writeElement("g:availability", "in stock");
                $xml->writeElement("g:price", $this->getPrice($product) . " UAH");
                $xml->startElement("g:brand");
                    $xml->writeCData($product->manufacturer->title);
                $xml->endElement();
                $xml->startElement("g:product_type");
                    $xml->writeCData($product->mainCategory->title);
                $xml->endElement();
            $xml->endElement();
            $this->doc .= $xml->outputMemory();
        }
    }

    private function hotlineProductsTemplate($name)
    {
        $xml = $this->xml;
            foreach($this->products as $product)
            {
                $xml->startElement($name);
                    $xml->writeElement("id",$product->id);
                    $xml->writeElement("name",$product->title);
                    $xml->writeElement("description",strip_tags($product->description));
                    $xml->writeElement('image',url($product->product->image));
                    foreach($product->photos as $photos)
                    {
                        $xml->writeElement('image',url($photos));
                    }
                    $xml->writeElement("url",url($product->slug));
                    $xml->writeElement("categoryId",$product->category_id);
                    $xml->writeElement("vendor",$product->manufacturer->title);
                    $xml->writeElement("stock","В наличии");
                    $xml->writeElement("priceRUAH",$this->getPrice($product));
                    $xml->writeElement("checkout",'true');
                    foreach($product->parameters as $parameter)
                    {
                        $xml->startElement("param");
                            $xml->writeAttribute("name",$parameter->attribute->title);
                            $xml->text(json_decode($parameter->title)->ru);
                        $xml->endelement();
                    }
                $xml->endElement();
                $this->doc .= $xml->outputMemory();
            }
    }

    private function hotlineCategoriesTemplate()
    {
        $xml = $this->xml;
        foreach($this->categories as $id => $data)
        {
            $xml->startElement("category");
                $xml->writeElement("id",$id);
                if($data['parent_id'])
                {
                    $xml->writeElement("parentId",$data['parent_id']);
                }
                $xml->writeElement("name",$data['name']);
            $xml->endElement();
            $this->doc .= $xml->outputMemory();
        }
    }

    private function priceCategoriesTemplate()
    {
        $xml = $this->xml;
        foreach($this->categories as $id => $data)
        {
            $xml->startElement("category");
                $xml->writeAttribute("id", $id);
                if($data['parent_id'])
                {
                    $xml->writeAttribute("parentId",$data['parent_id']);
                }
                $xml->text($data['name']);
            $xml->endElement();
            $this->doc .= $xml->outputMemory();
        }
    }

    private function priceProductsTemplate($name)
    {
        $xml = $this->xml;

            foreach($this->products as $product)
            {
                $xml->startElement($name);
                    $xml->writeAttribute("id",$product->id);
                    $xml->writeElement("name",$product->title);
                    $xml->writeElement("description",strip_tags($product->description));
                    $xml->writeElement('image',url($product->product->image));
                    foreach($product->photos as $photos)
                    {
                        $xml->writeElement('image',url($photos));
                    }
                    $xml->writeElement("url",url($product->slug));
                    $xml->writeElement("categoryId",$product->category_id);
                    $xml->writeElement("vendor",$product->manufacturer->title);
                    $xml->writeElement("stock","В наличии");
                    $xml->writeElement("priceRUAH",$this->getPrice($product));
                    $xml->writeElement("checkout",'true');
                    foreach($product->parameters as $parameter)
                    {
                        $xml->startElement("param");
                            $xml->writeAttribute("name",$parameter->attribute->title);
                            $xml->text(json_decode($parameter->title)->ru);
                        $xml->endelement();
                    }
                $xml->endElement();
                $this->doc .= $xml->outputMemory();
            }
    }

    private function yandexProductsTemplate($name)
    {
        $xml = $this->xml;

        foreach($this->products as $product)
        {
            $xml->startElement($name);
                $xml->writeAttribute("id",$product->id);
                $xml->writeAttribute("available","true");
                $xml->writeElement("name",$product->title);
                $xml->writeElement('picture',url($product->product->image));
                foreach($product->photos as $photos)
                {
                    $xml->writeElement('picture',url($photos));
                }
                $xml->writeElement("url",url($product->slug));
                $xml->writeElement("categoryId",$product->category_id);
                $xml->writeElement("vendor",$product->manufacturer->title);
                $xml->writeElement("stock","В наличии");
                $xml->writeElement("price",$this->getPrice($product));
                $xml->writeElement("currencyId","UAH");
                $xml->writeElement("checkout",'true');
                $xml->writeElement("delivery",'true');
                /*foreach($product->parameters as $parameter)
                {
                    $xml->startElement("param");
                        $xml->writeAttribute("name",$parameter->attribute->title);
                        $xml->text(json_decode($parameter->title)->ru);
                    $xml->endelement();
                }*/
            $xml->endElement();
            $this->doc .= $xml->outputMemory();
        }
    }

    private function yandexDescriptionProductsTemplate($name)
    {
        $xml = $this->xml;

        foreach($this->products as $product)
        {
            $xml->startElement($name);
                $xml->writeAttribute("id",$product->id);
                $xml->writeAttribute("available","true");
                $xml->writeElement("name",$product->title);
                $xml->writeElement("description",strip_tags($product->description));
                $xml->writeElement('picture',url($product->product->image));
                foreach($product->photos as $photos)
                {
                    $xml->writeElement('picture',url($photos));
                }
                $xml->writeElement("url",url($product->slug));
                $xml->writeElement("categoryId",$product->category_id);
                $xml->writeElement("vendor",$product->manufacturer->title);
                $xml->writeElement("stock","В наличии");
                $xml->writeElement("price",$this->getPrice($product));
                $xml->writeElement("currencyId","UAH");
                $xml->writeElement("checkout",'true');
                $xml->writeElement("delivery",'true');
                /*foreach($product->parameters as $parameter)
                {
                    $xml->startElement("param");
                        $xml->writeAttribute("name",$parameter->attribute->title);
                        $xml->text(json_decode($parameter->title)->ru);
                    $xml->endelement();
                }*/
            $xml->endElement();
            $this->doc .= $xml->outputMemory();
        }
    }

    private function yandexShortProductsTemplate($name)
    {
        $xml = $this->xml;

        foreach($this->products as $product)
        {
            $xml->startElement($name);
                $xml->writeAttribute("id",$product->id);
                $xml->writeAttribute("available","true");
                $xml->writeElement("name",$product->title);
                $xml->writeElement("url",url($product->slug));
                $xml->writeElement("vendor",$product->manufacturer->title);
                $xml->writeElement('typePrefix', $product->mainCategory->title);
                $xml->writeElement('picture',url($product->product->image));
                foreach($product->photos as $photos)
                {
                    $xml->writeElement('picture',url($photos));
                }
            $xml->endElement();
            $this->doc .= $xml->outputMemory();
        }
    }

    private function currenciesTemplate()
    {
        $xml = $this->xml;

        $xml->startElement("currencies");
            $xml->startElement("currency");
                $xml->writeAttribute("id", "UAH");
                $xml->writeAttribute("rate", "1.00");
            $xml->endElement();
        $xml->endElement();
        $this->doc .= $xml->outputMemory();
    }

    // Вспомогательный функции

    private function getProducts()
    {
        return self::where('slug',$this->type)->first()->product;
    }

    private function getCategories()
    {
        $categories = array();
        $categoriesObj = $this->products->pluck('maincategory');
        foreach($categoriesObj as $cat)
        {

            $categories[$cat->id] = [
                'name' => $cat->title,
                'parent_id' => $cat->parent_id
            ];
            if($cat->parent_id)
            {
                $parents = $this->getCategoryParents($cat);
                foreach($parents as $id => $data)
                {
                    $categories[$id] = $data;
                }
            }
        }
        ksort($categories);
        return $categories;
    }

    private function getCategoryParents(CategoryDescription $category)
    {
        $data = array();
        $data[$category->parent->id] =
        [
            'name' => $category->parent->title,
            'parent_id' => null
        ];
        if($category->parent->parent_id)
        {
            $data[$category->parent->id]['parent_id'] = $category->parent->parent_id;
            foreach($this->getCategoryParents($category->parent) as $id => $category_data)
            {
                $data[$id] = $category_data;
            }
        }
        return $data;
    }

    private function getPrice(ProductDescription $product)
    {
        $price = $this->price_marks[$product->product->price_mark];
        return $product->product->$price;
    }
}
