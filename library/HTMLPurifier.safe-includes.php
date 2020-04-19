<?php

declare(strict_types=1);

/**
 * @file
 * This file was auto-generated by generate-includes.php and includes all of
 * the core files required by HTML Purifier. This is a convenience stub that
 * includes all files using dirname(__FILE__) and require_once. PLEASE DO NOT
 * EDIT THIS FILE, changes will be overwritten the next time the script is run.
 *
 * Changes to include_path are not necessary.
 */

$__dir = __DIR__;

require_once $__dir . '/HTMLPurifier.php';
require_once $__dir . '/../src/Arborize.php';
require_once $__dir . '/../src/AttrCollections.php';
require_once $__dir . '/../src/AttrDef.php';
require_once $__dir . '/../src/AttrTransform.php';
require_once $__dir . '/../src/AttrTypes.php';
require_once $__dir . '/../src/AttrValidator.php';
require_once $__dir . '/HTMLPurifier/Bootstrap.php';
require_once $__dir . '/../src/Definition.php';
require_once $__dir . '/../src/CSSDefinition.php';
require_once $__dir . '/../src/ChildDef.php';
require_once $__dir . '/HTMLPurifier/Config.php';
require_once $__dir . '/../src/ConfigSchema.php';
require_once $__dir . '/../src/ContentSets.php';
require_once $__dir . '/../src/Context.php';
require_once $__dir . '/HTMLPurifier/DefinitionCache.php';
require_once $__dir . '/../src/DefinitionCacheFactory.php';
require_once $__dir . '/../src/Doctype.php';
require_once $__dir . '/../src/DoctypeRegistry.php';
require_once $__dir . '/../src/ElementDef.php';
require_once $__dir . '/../src/Encoder.php';
require_once $__dir . '/../src/EntityLookup.php';
require_once $__dir . '/../src/EntityParser.php';
require_once $__dir . '/../src/ErrorCollector.php';
require_once $__dir . '/../src/ErrorStruct.php';
require_once $__dir . '/HTMLPurifier/Exception.php';
require_once $__dir . '/../src/Filter.php';
require_once $__dir . '/../src/Generator.php';
require_once $__dir . '/../src/HTMLDefinition.php';
require_once $__dir . '/../src/HTMLModule.php';
require_once $__dir . '/../src/HTMLModuleManager.php';
require_once $__dir . '/../src/IDAccumulator.php';
require_once $__dir . '/../src/Injector.php';
require_once $__dir . '/../src/Language.php';
require_once $__dir . '/../src/LanguageFactory.php';
require_once $__dir . '/../src/Length.php';
require_once $__dir . '/HTMLPurifier/Lexer.php';
require_once $__dir . '/../src/Node.php';
require_once $__dir . '/../src/PercentEncoder.php';
require_once $__dir . '/../src/PropertyList.php';
require_once $__dir . '/../src/PropertyListIterator.php';
require_once $__dir . '/../src/Queue.php';
require_once $__dir . '/../src/Strategy.php';
require_once $__dir . '/../src/StringHash.php';
require_once $__dir . '/../src/StringHashParser.php';
require_once $__dir . '/../src/TagTransform.php';
require_once $__dir . '/../src/Token.php';
require_once $__dir . '/../src/TokenFactory.php';
require_once $__dir . '/../src/URI.php';
require_once $__dir . '/../src/URIDefinition.php';
require_once $__dir . '/../src/URIFilter.php';
require_once $__dir . '/../src/URIParser.php';
require_once $__dir . '/../src/URIScheme.php';
require_once $__dir . '/../src/URISchemeRegistry.php';
require_once $__dir . '/../src/UnitConverter.php';
require_once $__dir . '/../src/VarParser.php';
require_once $__dir . '/../src/VarParserException.php';
require_once $__dir . '/../src/Zipper.php';
require_once $__dir . '/../src/AttrDef/CSS.php';
require_once $__dir . '/../src/AttrDef/Cloner.php';
require_once $__dir . '/../src/AttrDef/Enum.php';
require_once $__dir . '/../src/AttrDef/Integer.php';
require_once $__dir . '/../src/AttrDef/Lang.php';
require_once $__dir . '/../src/AttrDef/Switcher.php';
require_once $__dir . '/../src/AttrDef/Text.php';
require_once $__dir . '/../src/AttrDef/URI.php';
require_once $__dir . '/../src/AttrDef/CSS/Number.php';
require_once $__dir . '/../src/AttrDef/CSS/AlphaValue.php';
require_once $__dir . '/../src/AttrDef/CSS/Background.php';
require_once $__dir . '/../src/AttrDef/CSS/BackgroundPosition.php';
require_once $__dir . '/../src/AttrDef/CSS/Border.php';
require_once $__dir . '/../src/AttrDef/CSS/Color.php';
require_once $__dir . '/../src/AttrDef/CSS/Composite.php';
require_once $__dir . '/../src/AttrDef/CSS/DenyElementDecorator.php';
require_once $__dir . '/../src/AttrDef/CSS/Filter.php';
require_once $__dir . '/../src/AttrDef/CSS/Font.php';
require_once $__dir . '/../src/AttrDef/CSS/FontFamily.php';
require_once $__dir . '/../src/AttrDef/CSS/Ident.php';
require_once $__dir . '/../src/AttrDef/CSS/ImportantDecorator.php';
require_once $__dir . '/../src/AttrDef/CSS/Length.php';
require_once $__dir . '/../src/AttrDef/CSS/ListStyle.php';
require_once $__dir . '/../src/AttrDef/CSS/Multiple.php';
require_once $__dir . '/../src/AttrDef/CSS/Percentage.php';
require_once $__dir . '/../src/AttrDef/CSS/TextDecoration.php';
require_once $__dir . '/../src/AttrDef/CSS/URI.php';
require_once $__dir . '/../src/AttrDef/HTML/Boolean.php';
require_once $__dir . '/../src/AttrDef/HTML/Nmtokens.php';
require_once $__dir . '/../src/AttrDef/HTML/Classname.php';
require_once $__dir . '/../src/AttrDef/HTML/Color.php';
require_once $__dir . '/../src/AttrDef/HTML/FrameTarget.php';
require_once $__dir . '/../src/AttrDef/HTML/ID.php';
require_once $__dir . '/../src/AttrDef/HTML/Pixels.php';
require_once $__dir . '/../src/AttrDef/HTML/Length.php';
require_once $__dir . '/../src/AttrDef/HTML/LinkTypes.php';
require_once $__dir . '/../src/AttrDef/HTML/MultiLength.php';
require_once $__dir . '/../src/AttrDef/URI/Email.php';
require_once $__dir . '/../src/AttrDef/URI/Host.php';
require_once $__dir . '/../src/AttrDef/URI/IPv4.php';
require_once $__dir . '/../src/AttrDef/URI/IPv6.php';
require_once $__dir . '/../src/AttrDef/URI/Email/SimpleCheck.php';
require_once $__dir . '/../src/AttrTransform/Background.php';
require_once $__dir . '/../src/AttrTransform/BdoDir.php';
require_once $__dir . '/../src/AttrTransform/BgColor.php';
require_once $__dir . '/../src/AttrTransform/BoolToCSS.php';
require_once $__dir . '/../src/AttrTransform/Border.php';
require_once $__dir . '/../src/AttrTransform/EnumToCSS.php';
require_once $__dir . '/../src/AttrTransform/ImgRequired.php';
require_once $__dir . '/../src/AttrTransform/ImgSpace.php';
require_once $__dir . '/../src/AttrTransform/Input.php';
require_once $__dir . '/../src/AttrTransform/Lang.php';
require_once $__dir . '/../src/AttrTransform/Length.php';
require_once $__dir . '/../src/AttrTransform/Name.php';
require_once $__dir . '/../src/AttrTransform/NameSync.php';
require_once $__dir . '/../src/AttrTransform/Nofollow.php';
require_once $__dir . '/../src/AttrTransform/SafeEmbed.php';
require_once $__dir . '/../src/AttrTransform/SafeObject.php';
require_once $__dir . '/../src/AttrTransform/SafeParam.php';
require_once $__dir . '/../src/AttrTransform/ScriptRequired.php';
require_once $__dir . '/../src/AttrTransform/TargetBlank.php';
require_once $__dir . '/../src/AttrTransform/TargetNoopener.php';
require_once $__dir . '/../src/AttrTransform/TargetNoreferrer.php';
require_once $__dir . '/../src/AttrTransform/Textarea.php';
require_once $__dir . '/../src/ChildDef/Chameleon.php';
require_once $__dir . '/../src/ChildDef/Custom.php';
require_once $__dir . '/../src/ChildDef/Nothing.php';
require_once $__dir . '/../src/ChildDef/Lists.php';
require_once $__dir . '/../src/ChildDef/Required.php';
require_once $__dir . '/../src/ChildDef/Optional.php';
require_once $__dir . '/../src/ChildDef/StrictBlockquote.php';
require_once $__dir . '/../src/ChildDef/Table.php';
require_once $__dir . '/../src/DefinitionCache/Decorator.php';
require_once $__dir . '/../src/DefinitionCache/DevNull.php';
require_once $__dir . '/../src/DefinitionCache/Serializer.php';
require_once $__dir . '/HTMLPurifier/DefinitionCache/Decorator/Cleanup.php';
require_once $__dir . '/HTMLPurifier/DefinitionCache/Decorator/Memory.php';
require_once $__dir . '/../src/HTMLModule/Bdo.php';
require_once $__dir . '/../src/HTMLModule/CommonAttributes.php';
require_once $__dir . '/../src/HTMLModule/Edit.php';
require_once $__dir . '/../src/HTMLModule/Forms.php';
require_once $__dir . '/../src/HTMLModule/Hypertext.php';
require_once $__dir . '/../src/HTMLModule/Iframe.php';
require_once $__dir . '/../src/HTMLModule/Image.php';
require_once $__dir . '/../src/HTMLModule/Legacy.php';
require_once $__dir . '/../src/HTMLModule/Lists.php';
require_once $__dir . '/../src/HTMLModule/Name.php';
require_once $__dir . '/../src/HTMLModule/Nofollow.php';
require_once $__dir . '/../src/HTMLModule/NonXMLCommonAttributes.php';
require_once $__dir . '/../src/HTMLModule/Objects.php';
require_once $__dir . '/../src/HTMLModule/Presentation.php';
require_once $__dir . '/../src/HTMLModule/Proprietary.php';
require_once $__dir . '/../src/HTMLModule/Ruby.php';
require_once $__dir . '/../src/HTMLModule/SafeEmbed.php';
require_once $__dir . '/../src/HTMLModule/SafeObject.php';
require_once $__dir . '/../src/HTMLModule/SafeScripting.php';
require_once $__dir . '/../src/HTMLModule/Scripting.php';
require_once $__dir . '/../src/HTMLModule/StyleAttribute.php';
require_once $__dir . '/../src/HTMLModule/Tables.php';
require_once $__dir . '/../src/HTMLModule/Target.php';
require_once $__dir . '/../src/HTMLModule/TargetBlank.php';
require_once $__dir . '/../src/HTMLModule/TargetNoopener.php';
require_once $__dir . '/../src/HTMLModule/TargetNoreferrer.php';
require_once $__dir . '/../src/HTMLModule/Text.php';
require_once $__dir . '/../src/HTMLModule/Tidy.php';
require_once $__dir . '/../src/HTMLModule/XMLCommonAttributes.php';
require_once $__dir . '/../src/HTMLModule/Tidy/Name.php';
require_once $__dir . '../src/HTMLModule/Tidy/Proprietary.php';
require_once $__dir . '../src/HTMLModule/Tidy/XHTMLAndHTML4.php';
require_once $__dir . '../src/HTMLModule/Tidy/Strict.php';
require_once $__dir . '../src/HTMLModule/Tidy/Transitional.php';
require_once $__dir . '../src/HTMLModule/Tidy/XHTML.php';
require_once $__dir . '../src/Injector/AutoParagraph.php';
require_once $__dir . '../src/Injector/DisplayLinkURI.php';
require_once $__dir . '../src/Injector/Linkify.php';
require_once $__dir . '../src/Injector/PurifierLinkify.php';
require_once $__dir . '../src/Injector/RemoveEmpty.php';
require_once $__dir . '../src/Injector/RemoveSpansWithoutAttributes.php';
require_once $__dir . '../src/Injector/SafeObject.php';
require_once $__dir . '/../src/Lexer/DOMLex.php';
require_once $__dir . '/HTMLPurifier/Lexer/DirectLex.php';
require_once $__dir . '/../src/Node/Comment.php';
require_once $__dir . '/../src/Node/Element.php';
require_once $__dir . '/../src/Node/Text.php';
require_once $__dir . '/../src/Strategy/Composite.php';
require_once $__dir . '/../src/Strategy/Core.php';
require_once $__dir . '/../src/Strategy/FixNesting.php';
require_once $__dir . '/../src/Strategy/MakeWellFormed.php';
require_once $__dir . '/../src/Strategy/RemoveForeignElements.php';
require_once $__dir . '/../src/Strategy/ValidateAttributes.php';
require_once $__dir . '/../src/TagTransform/Font.php';
require_once $__dir . '/../src/TagTransform/Simple.php';
require_once $__dir . '/../src/Token/Comment.php';
require_once $__dir . '/../src/Token/Tag.php';
require_once $__dir . '/../src/Token/EmptyToken.php';
require_once $__dir . '/../src/Token/End.php';
require_once $__dir . '/../src/Token/Start.php';
require_once $__dir . '/../src/Token/Text.php';
require_once $__dir . '/../src/URIFilter/DisableExternal.php';
require_once $__dir . '/../src/URIFilter/DisableExternalResources.php';
require_once $__dir . '/../src/URIFilter/DisableResources.php';
require_once $__dir . '/../src/URIFilter/HostBlacklist.php';
require_once $__dir . '/../src/URIFilter/MakeAbsolute.php';
require_once $__dir . '/../src/URIFilter/Munge.php';
require_once $__dir . '/../src/URIFilter/SafeIframe.php';
require_once $__dir . '/HTMLPurifier/URIScheme/data.php';
require_once $__dir . '/HTMLPurifier/URIScheme/file.php';
require_once $__dir . '/HTMLPurifier/URIScheme/ftp.php';
require_once $__dir . '/HTMLPurifier/URIScheme/http.php';
require_once $__dir . '/HTMLPurifier/URIScheme/https.php';
require_once $__dir . '/HTMLPurifier/URIScheme/mailto.php';
require_once $__dir . '/HTMLPurifier/URIScheme/news.php';
require_once $__dir . '/HTMLPurifier/URIScheme/nntp.php';
require_once $__dir . '/HTMLPurifier/URIScheme/tel.php';
require_once $__dir . '/HTMLPurifier/VarParser/Flexible.php';
require_once $__dir . '/HTMLPurifier/VarParser/Native.php';
