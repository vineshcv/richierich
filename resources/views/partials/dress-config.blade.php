@php
  $waDigits = \App\Models\Store::siteWhatsapp();
  $catalog = $catalog ?? ['categories' => [], 'products' => [], 'combos' => [], 'season' => []];
@endphp
<script>
  window.DRESS_CONFIG = {
    whatsapp: @json($waDigits),
    siteName: @json('Richierich'),
    logo: @json(asset('assets/dress_logo.png').'?v=4'),
    mark: @json(asset('assets/dress_mark.png').'?v=1'),
    icon: @json(asset('assets/dress_icon-192.png').'?v=3'),
    defaultImage: @json(asset('assets/dress_prod-red-saree.png')),
    sw: @json(asset('sw.js')),
    routes: {
      home: @json(route('home')),
      shop: @json(route('shop.products')),
      product: @json(url('/product').'/__ID__'),
      cart: @json(route('shop.cart')),
      contact: @json(route('shop.contact')),
      combos: @json(route('shop.combos')),
      season: @json(route('shop.season')),
      bulk: @json(route('shop.bulk'))
    },
    flags: {
      share: @json((bool) ($settings->show_share ?? true)),
      whatsapp: @json((bool) ($settings->show_whatsapp ?? true)),
      cart: @json((bool) ($settings->show_cart ?? true)),
      view: @json((bool) ($settings->show_view ?? true)),
      detailPage: true
    }
  };
  window.DRESS_CATEGORIES = @json($catalog['categories']);
  window.DRESS_PRODUCTS = @json($catalog['products']);
  window.DRESS_COMBOS = @json($catalog['combos']);
  window.DRESS_SEASON_OFFERS = @json($catalog['season']);
  window.getDressProduct = function (id) {
    var fromProducts = (window.DRESS_PRODUCTS || []).find(function (p) { return p.id === id; });
    if (fromProducts) return fromProducts;
    var fromSeason = (window.DRESS_SEASON_OFFERS || []).find(function (p) { return p.id === id; });
    if (fromSeason) {
      return Object.assign({
        brand: "Season Offer",
        category: "season",
        short: fromSeason.blurb,
        description: fromSeason.blurb,
        bullets: fromSeason.includes,
        specs: [["Type", "Season offer"]],
        gallery: [fromSeason.image]
      }, fromSeason);
    }
    return null;
  };
  window.getDressCategory = function (id) {
    return (window.DRESS_CATEGORIES || []).find(function (c) { return c.id === id; });
  };
</script>
