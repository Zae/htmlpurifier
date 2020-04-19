<?php

declare(strict_types=1);

/**
 * @file
 * This file was auto-generated by generate-includes.php and includes all of
 * the core files required by HTML Purifier. Use this if performance is a
 * primary concern and you are using an opcode cache. PLEASE DO NOT EDIT THIS
 * FILE, changes will be overwritten the next time the script is run.
 *
 * @version 4.13.0
 *
 * @warning
 *      You must *not* include any other HTML Purifier files before this file,
 *      because 'require' not 'require_once' is used.
 *
 * @warning
 *      This file requires that the include path contains the HTML Purifier
 *      library directory; this is not auto-set.
 */

require 'HTMLPurifier.php';
require '../src/Arborize.php';
require '../src/AttrCollections.php';
require '../src/AttrDef.php';
require '../src/AttrTransform.php';
require '../src/AttrTypes.php';
require '../src/AttrValidator.php';
require 'HTMLPurifier/Bootstrap.php';
require '../src/Definition.php';
require '../src/CSSDefinition.php';
require '../src/ChildDef.php';
require 'HTMLPurifier/Config.php';
require '../src/ConfigSchema.php';
require '../src/ContentSets.php';
require '../src/Context.php';
require '../src/DefinitionCache.php';
require '../src/DefinitionCacheFactory.php';
require '../src/Doctype.php';
require '../src/DoctypeRegistry.php';
require '../src/ElementDef.php';
require '../src/Encoder.php';
require '../src/EntityLookup.php';
require '../src/EntityParser.php';
require '../src/ErrorCollector.php';
require '../src/ErrorStruct.php';
require '../src/Exception.php';
require '../src/Filter.php';
require '../src/Generator.php';
require '../src/HTMLDefinition.php';
require '../src/HTMLModule.php';
require '../src/HTMLModuleManager.php';
require '../src/IDAccumulator.php';
require '../src/Injector.php';
require '../src/Language.php';
require '../src/LanguageFactory.php';
require '../src/Length.php';
require 'HTMLPurifier/Lexer.php';
require '../src/Node.php';
require '../src/PercentEncoder.php';
require '../src/PropertyList.php';
require '../src/PropertyListIterator.php';
require '../src/Queue.php';
require '../src/Strategy.php';
require '../src/StringHash.php';
require '../src/StringHashParser.php';
require '../src/TagTransform.php';
require '../src/Token.php';
require '../src/TokenFactory.php';
require '../src/URI.php';
require '../src/URIDefinition.php';
require '../src/URIFilter.php';
require '../src/URIParser.php';
require '../src/URIScheme.php';
require '../src/URISchemeRegistry.php';
require '../src/UnitConverter.php';
require '../src/VarParser.php';
require '../src/VarParserException.php';
require '../src/Zipper.php';
require '../src/AttrDef/CSS.php';
require '../src/AttrDef/Cloner.php';
require '../src/AttrDef/Enum.php';
require '../src/AttrDef/Integer.php';
require '../src/AttrDef/Lang.php';
require '../src/AttrDef/Switcher.php';
require '../src/AttrDef/Text.php';
require '../src/AttrDef/URI.php';
require '../src/AttrDef/CSS/Number.php';
require '../src/AttrDef/CSS/AlphaValue.php';
require '../src/AttrDef/CSS/Background.php';
require '../src/AttrDef/CSS/BackgroundPosition.php';
require '../src/AttrDef/CSS/Border.php';
require '../src/AttrDef/CSS/Color.php';
require '../src/AttrDef/CSS/Composite.php';
require '../src/AttrDef/CSS/DenyElementDecorator.php';
require '../src/AttrDef/CSS/Filter.php';
require '../src/AttrDef/CSS/Font.php';
require '../src/AttrDef/CSS/FontFamily.php';
require '../src/AttrDef/CSS/Ident.php';
require '../src/AttrDef/CSS/ImportantDecorator.php';
require '../src/AttrDef/CSS/Length.php';
require '../src/AttrDef/CSS/ListStyle.php';
require '../src/AttrDef/CSS/Multiple.php';
require '../src/AttrDef/CSS/Percentage.php';
require '../src/AttrDef/CSS/TextDecoration.php';
require '../src/AttrDef/CSS/URI.php';
require '../src/AttrDef/HTML/Boolean.php';
require '../src/AttrDef/HTML/Nmtokens.php';
require '../src/AttrDef/HTML/Classname.php';
require '../src/AttrDef/HTML/Color.php';
require '../src/AttrDef/HTML/FrameTarget.php';
require '../src/AttrDef/HTML/ID.php';
require '../src/AttrDef/HTML/Pixels.php';
require '../src/AttrDef/HTML/Length.php';
require '../src/AttrDef/HTML/LinkTypes.php';
require '../src/AttrDef/HTML/MultiLength.php';
require '../src/AttrDef/URI/Email.php';
require '../src/AttrDef/URI/Host.php';
require '../src/AttrDef/URI/IPv4.php';
require '../src/AttrDef/URI/IPv6.php';
require '../src/AttrDef/URI/Email/SimpleCheck.php';
require '../src/AttrTransform/Background.php';
require '../src/AttrTransform/BdoDir.php';
require '../src/AttrTransform/BgColor.php';
require '../src/AttrTransform/BoolToCSS.php';
require '../src/AttrTransform/Border.php';
require '../src/AttrTransform/EnumToCSS.php';
require '../src/AttrTransform/ImgRequired.php';
require '../src/AttrTransform/ImgSpace.php';
require '../src/AttrTransform/Input.php';
require '../src/AttrTransform/Lang.php';
require '../src/AttrTransform/Length.php';
require '../src/AttrTransform/Name.php';
require '../src/AttrTransform/NameSync.php';
require '../src/AttrTransform/Nofollow.php';
require '../src/AttrTransform/SafeEmbed.php';
require '../src/AttrTransform/SafeObject.php';
require '../src/AttrTransform/SafeParam.php';
require '../src/AttrTransform/ScriptRequired.php';
require '../src/AttrTransform/TargetBlank.php';
require '../src/AttrTransform/TargetNoopener.php';
require '../src/AttrTransform/TargetNoreferrer.php';
require '../src/AttrTransform/Textarea.php';
require '../src/ChildDef/Chameleon.php';
require '../src/ChildDef/Custom.php';
require '../src/ChildDef/Nothing.php';
require '../src/ChildDef/Lists.php';
require '../src/ChildDef/Required.php';
require '../src/ChildDef/Optional.php';
require '../src/ChildDef/StrictBlockquote.php';
require '../src/ChildDef/Table.php';
require '../src/DefinitionCache/Decorator.php';
require '../src/DefinitionCache/DevNull.php';
require '../src/DefinitionCache/Serializer.php';
require 'HTMLPurifier/DefinitionCache/Decorator/Cleanup.php';
require 'HTMLPurifier/DefinitionCache/Decorator/Memory.php';
require '../src/HTMLModule/Bdo.php';
require '../src/HTMLModule/CommonAttributes.php';
require '../src/HTMLModule/Edit.php';
require '../src/HTMLModule/Forms.php';
require '../src/HTMLModule/Hypertext.php';
require '../src/HTMLModule/Iframe.php';
require '../src/HTMLModule/Image.php';
require '../src/HTMLModule/Legacy.php';
require '../src/HTMLModule/Lists.php';
require '../src/HTMLModule/Name.php';
require '../src/HTMLModule/Nofollow.php';
require '../src/HTMLModule/NonXMLCommonAttributes.php';
require '../src/HTMLModule/Objects.php';
require '../src/HTMLModule/Presentation.php';
require '../src/HTMLModule/Proprietary.php';
require '../src/HTMLModule/Ruby.php';
require '../src/HTMLModule/SafeEmbed.php';
require '../src/HTMLModule/SafeObject.php';
require '../src/HTMLModule/SafeScripting.php';
require '../src/HTMLModule/Scripting.php';
require '../src/HTMLModule/StyleAttribute.php';
require '../src/HTMLModule/Tables.php';
require '../src/HTMLModule/Target.php';
require '../src/HTMLModule/TargetBlank.php';
require '../src/HTMLModule/TargetNoopener.php';
require '../src/HTMLModule/TargetNoreferrer.php';
require '../src/HTMLModule/Text.php';
require '../src/HTMLModule/Tidy.php';
require '../src/HTMLModule/XMLCommonAttributes.php';
require '../src/HTMLModule/Tidy/Name.php';
require '../src/HTMLModule/Tidy/Proprietary.php';
require '../src/HTMLModule/Tidy/XHTMLAndHTML4.php';
require '../src/HTMLModule/Tidy/Strict.php';
require '../src/HTMLModule/Tidy/Transitional.php';
require '../src/HTMLModule/Tidy/XHTML.php';
require '../src/Injector/AutoParagraph.php';
require '../src/Injector/DisplayLinkURI.php';
require '../src/Injector/Linkify.php';
require '../src/Injector/PurifierLinkify.php';
require '../src/Injector/RemoveEmpty.php';
require '../src/Injector/RemoveSpansWithoutAttributes.php';
require '../src/Injector/SafeObject.php';
require '../src/Lexer/DOMLex.php';
require 'HTMLPurifier/Lexer/DirectLex.php';
require '../src/Node/Comment.php';
require '../src/Node/Element.php';
require '../src/Node/Text.php';
require '../src/Strategy/Composite.php';
require '../src/Strategy/Core.php';
require '../src/Strategy/FixNesting.php';
require '../src/Strategy/MakeWellFormed.php';
require '../src/Strategy/RemoveForeignElements.php';
require '../src/Strategy/ValidateAttributes.php';
require '../src/TagTransform/Font.php';
require '../src/TagTransform/Simple.php';
require '../src/Token/Comment.php';
require '../src/Token/Tag.php';
require '../src/Token/EmptyToken.php';
require '../src/Token/End.php';
require '../src/Token/Start.php';
require '../src/Token/Text.php';
require '../src/URIFilter/DisableExternal.php';
require '../src/URIFilter/DisableExternalResources.php';
require '../src/URIFilter/DisableResources.php';
require '../src/URIFilter/HostBlacklist.php';
require '../src/URIFilter/MakeAbsolute.php';
require '../src/URIFilter/Munge.php';
require '../src/URIFilter/SafeIframe.php';
require '../src/URIScheme/data.php';
require '../src/URIScheme/file.php';
require '../src/URIScheme/ftp.php';
require '../src/URIScheme/http.php';
require '../src/URIScheme/https.php';
require '../src/URIScheme/mailto.php';
require '../src/URIScheme/news.php';
require '../src/URIScheme/nntp.php';
require '../src/URIScheme/tel.php';
require '../src/VarParser/Flexible.php';
require '../src/VarParser/Native.php';
