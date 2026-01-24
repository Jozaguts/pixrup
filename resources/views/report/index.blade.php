<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>
        Property Report – {{ $property->title }}
    </title>
    <style>
        @page {
            size: A4;
            margin: 12.5mm 10mm 12.5mm 10mm !important;
            background: #FAF9F6;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        header {
            position: relative;
            width: 100%;
        }

        header .property-name, header span {
            vertical-align: middle;
            font-weight: 600;
            font-size: 16px;
            color: black;
            display:inline-block;
        }

        /*image styles*/
        .polaroid {
            background-color: #fff;
            width: calc(100% - 6rem);
            box-shadow: 5px 7px 4px #888888;
            margin: 1.5rem auto;
            display: block;
            position: relative;
        }

        .polaroid-caption{
            position:relative;
            width: calc(100% - 20px);
            margin-bottom:0.5rem;
            font-size: 16px;
            text-align: center;
            line-height: 2em;
            font-weight: 600;
        }

        img {
            width: 100%;
        }

        .warning-note {
            background-color: #eeeeee; /* off-white */
            border: 1px solid #D1D1D1;
            padding: 12px 14px;
            font-size: 16px;
            line-height: 1.4;
            color: black;
            font-weight: bold;
        }

        .warning-note strong {
            font-size: 41px;
        }

        .page-break {
            page-break-after: always;
        }

        section {
            margin: 24px 5mm 1rem;
        }

        section h2 {
            font-size: 18px;
            font-weight: 500;
            color: #1a1a1a;
            font-variant:small-caps;
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            border:solid lightgrey 1px;
            border-radius:5px;
            overflow:hidden;
        }

        table thead tr th {
            text-align: left;
            padding: 14px 16px 14px 0;
            font-weight: bolder;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 1px;
            color:black;
        }

        table thead tr th:last-child {
            padding-right: 1.5rem;
            text-align: right;
        }

        table thead tr th:first-child {
            padding-left: 1.5rem;
            text-align: left;
        }

        table thead tr {
            background-color: #eeeeee;
        }

        table tbody tr:nth-of-type(even) {
            background-color: #f9fafd;
        }

        table tbody tr td {
            padding: 14px 16px 14px 0;
            font-weight: 400;
            font-size:12px;
            color: #1a1a1a;
            text-align: left;
        }

        table tbody tr td:first-child {
            padding-left: 1.5rem;
            text-align: left;
        }

        table tbody tr td:last-child {
            padding-right: 1.5rem;
            text-align: right;
        }
    </style>
</head>

<body>
        {{-- Report Header --}}
      <header>
          <table style="width: 100%; border-collapse: collapse;">
              <tr>
                  <td style="text-align: left">
                      <h1 class="property-name">
                          <strong>PixrUp</strong> | Property Report
                      </h1>
                  </td>
                  <td style="text-align: right">
                    <span>{{now()->format('F j, Y')}}</span>
                  </td>
              </tr>
          </table>
      </header>
       <x-polaroid-image :imageUrl="$property_image" imageLabel="{{ $property->title }}" />

       <x-property-details :property="$property" :overview="$overview" />

        <div class="page-break"></div>
{{--        Property Worth--}}

        <x-property-worth-section :worth="$worth" :runes="$runes" />

        <x-pix-vision-rune-report :items="$runes['pix_worth']"/>

        {{-- Sales History--}}
       <x-property-sales-history :sales="$sales_history"/>

        <div class="page-break"></div>



        <x-sale-comparables-section :cache="$spy_hunt_cache"/>

        <x-pix-vision-rune-report :items="$runes['pix_hunt']"/>

        <x-rent-comparables-section :cache="$spy_hunt_cache"/>
{{--        Spy Hunt--}}

       <x-spy-hunt-section :cache="$spy_hunt_cache"/>
        <div class="page-break"></div>
{{--        Glow Ups--}}
       <x-glow-up-section :glowUps="$glow_up" :runes="$runes" :icon="$icon"/>

</body>
</html>

