@props(['propertyName', 'logo'])
<header>
    <table width="100%" cellspacing="0" cellpadding="0"
           style="padding-left: 1rem; padding-right: 1rem;">
        <tr>
            <td style="vertical-align: middle;">
                <img
                    src="{{ $logo }}"
                    alt="Logo"
                    style="display:block;"
                >
            </td>

            <td style="vertical-align: center; text-align: right;">
                <span class="property-name">{{now()->translatedFormat('F jS Y')}}</span>
            </td>
        </tr>
    </table>
</header>

<div class="content">
    {{$slot}}
</div>
