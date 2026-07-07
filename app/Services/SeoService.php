<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;

class SeoService
{
    public function productSchema(Product $product): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->short_description,
            'sku' => $product->sku,
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'BDT',
                'price' => $product->price,
                'availability' => $product->isInStock()
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
        ];
    }

    public function breadcrumbSchema(array $items): array
    {
        $list = [];
        foreach ($items as $position => $item) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $position + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
    }

    public function organizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => Setting::get('site_name', 'Dinajpur IT Park'),
            'url' => config('app.url'),
            'description' => Setting::get('meta_description', 'Premium computer accessories shop'),
        ];
    }

    public function getMetaData(): array
    {
        $siteName = Setting::get('site_name', 'Dinajpur IT Park');
        $siteTagline = Setting::get('site_tagline', 'Your Ultimate Destination for Premium Computer Accessories');
        $defaultMetaTitle = Setting::get('meta_title', $siteName.' - '.$siteTagline);
        $defaultMetaDescription = Setting::get('meta_description', __('general.meta_description_default'));

        $logo = Setting::get('site_logo');
        $defaultOgImage = $logo ? asset('storage/'.$logo) : asset('images/og-default.jpg');

        $view = view();
        $titleSection = $view->getSections()['title'] ?? '';
        $metaDescriptionSection = $view->getSections()['meta_description'] ?? '';

        $title = $titleSection ? strip_tags($titleSection) : $defaultMetaTitle;
        $metaDescription = $metaDescriptionSection ? strip_tags($metaDescriptionSection) : $defaultMetaDescription;

        $ogTitleSection = $view->getSections()['og_title'] ?? '';
        $ogDescriptionSection = $view->getSections()['og_description'] ?? '';
        $ogImageSection = $view->getSections()['og_image'] ?? '';
        $ogTypeSection = $view->getSections()['og_type'] ?? '';

        $ogTitle = $ogTitleSection ? strip_tags($ogTitleSection) : $title;
        $ogDescription = $ogDescriptionSection ? strip_tags($ogDescriptionSection) : $metaDescription;
        $ogImage = $ogImageSection ? strip_tags($ogImageSection) : $defaultOgImage;
        $ogType = $ogTypeSection ? strip_tags($ogTypeSection) : 'website';

        return [
            'siteName' => $siteName,
            'siteTagline' => $siteTagline,
            'title' => $title,
            'metaDescription' => $metaDescription,
            'ogTitle' => $ogTitle,
            'ogDescription' => $ogDescription,
            'ogImage' => $ogImage,
            'ogType' => $ogType,
        ];
    }
}
