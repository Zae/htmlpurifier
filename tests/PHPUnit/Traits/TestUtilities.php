<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Traits;

use HTMLPurifier\Config;
use HTMLPurifier\HTMLPurifier;

/**
 * Trait TestUtilities
 *
 * @package HTMLPurifier\Tests\Traits
 */
trait TestUtilities
{
    /**
     * We load the default HtmlPurifierConfig, so here provide all kinds of overrides to see if they work.
     *
     * @return array
     */
    public function configProvider(): array
    {
        return [
            [['Attr.AllowedFrameTargets' => ['_blank']]],
            [['Attr.AllowedFrameTargets' => ['_blank', '_self', '_top', '_parent']]],
            [['Attr.AllowedRel' => ['noreferrer']]],
            [['Attr.AllowedRel' => ['noreferrer', 'noopener']]],
            [['Attr.ClassUseCDATA' => true]],
            [['Attr.ClassUseCDATA' => false]],
            [['Attr.DefaultImageAlt' => 'somedefaultaltvalue']],
            [['Attr.DefaultInvalidImage' => 'somedefaultsrcvalue']],
            [['Attr.DefaultInvalidImageAlt' => 'someotherdefaultvalue']],
            [['Attr.DefaultTextDir' => 'rtl']],
            [['Attr.EnableID' => true]],
            [['Attr.ID.HTML5' => true]],
            [['Attr.IDPrefix' => 'IdPrefix']],
            [['Attr.IDPrefixLocal' => 'IdPrefixLocal']],

            [['AutoFormat.AutoParagraph' => true]],
            [['AutoFormat.DisplayLinkURI' => true]],
//            [['AutoFormat.Linkify' => true]],
            [['AutoFormat.PurifierLinkify' => true]],
            [['AutoFormat.RemoveEmpty.RemoveNbsp' => true]],
            [['AutoFormat.RemoveEmpty' => true]],
            [['AutoFormat.RemoveSpansWithoutAttributes' => true]],

            [['CSS.AllowDuplicates' => true]],
            [['CSS.AllowImportant' => true]],
            [['CSS.AllowTricky' => true]],
            [['CSS.MaxImgLength' => '10px']],
            [['CSS.MaxImgLength' => '200px']],
            [['CSS.MaxImgLength' => '1800px']],
            [['CSS.MaxImgLength' => '1cm']],
            [['CSS.MaxImgLength' => '10cm']],
            [['CSS.MaxImgLength' => '50cm']],
            [['CSS.Proprietary' => true]],
            [['CSS.Trusted' => true]],

            [['Core.AggressivelyFixLt' => false]],
            [['Core.AggressivelyRemoveScript' => false]],
            [['Core.AllowHostnameUnderscore' => true]],
            [['Core.AllowHostnameUnderscore' => true]],
            [['Core.ConvertDocumentToFragment' => false]],
            [['Core.DisableExcludes' => true]],
            [['Core.EnableIDNA' => true]],
            [['Core.EscapeInvalidChildren' => true]],
            [['Core.EscapeInvalidTags' => true]],
            [['Core.EscapeNonASCIICharacters' => true]],
            [['Core.HiddenElements' => []]],
            [['Core.HiddenElements' => ['p' => true]]],
            [['Core.NormalizeNewlines' => false]],
            [['Core.RemoveInvalidImg' => false]],
            [['Core.RemoveProcessingInstructions' => true]],

//            [['Filter.ExtractStyleBlocks.Escaping' => false]],
//            [['Filter.ExtractStyleBlocks' => true]],

            [['HTML.Allowed' => 'div[class],p,a[href]']],
            [['HTML.AllowedAttributes' => ['*.class', '*.src', '*.href']]],
            [['HTML.AllowedElements' => ['div', 'p', 'a']]],

            [['HTML.Attr.Name.UseCDATA' => true]],
            [['HTML.BlockWrapper' => 'div']],
            [['HTML.Doctype' => 'HTML 4.01 Transitional']],
            [['HTML.FlashAllowFullScreen' => true]],
            [['HTML.MaxImgLength' => 10]],
            [['HTML.MaxImgLength' => 100]],
            [['HTML.MaxImgLength' => 400]],
            [['HTML.MaxImgLength' => 8000]],
            [['HTML.Nofollow' => true]],
            [['HTML.Parent' => 'span']],
            [['HTML.SafeEmbed' => true]],
            [['HTML.SafeObject' => true]],
            [['HTML.TargetBlank' => true]],
            [['HTML.TargetNoopener' => false]],
            [['HTML.TargetNoreferrer' => false]],
            [['HTML.TidyLevel' => 'none']],
            [['HTML.TidyLevel' => 'light']],
            [['HTML.TidyLevel' => 'heavy']],
            [['HTML.Trusted' => true]],

            [['Output.CommentScriptContents' => false]],
            [['Output.FixInnerHTML' => false]],
            [['Output.FlashCompat' => true]],
            [['Output.Newline' => "\r\n"]],
            [['Output.Newline' => "\r"]],
            [['Output.Newline' => "\n"]],
            [['Output.Newline' => 'banana']],
            [['Output.SortAttr' => true]],
            [['Output.TidyFormat' => true]],

            [['URI.Base' => 'uriBase']],
            [['URI.DefaultScheme' => 'https']],
            [['URI.DefaultScheme' => 'ftp']],
            [['URI.DefaultScheme' => null]],
            [['URI.Disable' => true]],
            [['URI.DisableExternal' => true]],
            [['URI.DisableExternalResources' => true]],
            [['URI.DisableResources' => true]],
            [['URI.Base' => 'https://example.com', 'URI.MakeAbsolute' => true]],
            [['URI.Munge' => 'https://examle.com?u=%s']],
            [['URI.MungeResources' => true]],
            [['URI.MungeSecretKey' => 'secret']],
            [['URI.OverrideAllowedSchemes' => false]],
            [['HTML.SafeIframe' => true], ['URI.SafeIframeRegexp' => '%^http://www.youtube.com/embed/%']],
            [['HTML.SafeIframe' => true], ['URI.SafeIframeRegexp' => '%^http://player.vimeo.com/video/%']],
            [['HTML.SafeIframe' => true], ['URI.SafeIframeRegexp' => '%^http://(www.youtube.com/embed/|player.vimeo.com/video/)%']],
        ];
    }

    /**
     * @param array $extraConfig
     *
     * @return HTMLPurifier
     */
    private function createHtmlPurifier(array $extraConfig = []): HTMLPurifier
    {
        $config = Config::createDefault();
        $config->loadArray($extraConfig);

        $def = $config->getHTMLDefinition(true);

        // http://developers.whatwg.org/grouping-content.html
        $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
        $def->addElement('figcaption', 'Inline', 'Flow', 'Common');

        return new HTMLPurifier($config);
    }
}
