<?php

declare(strict_types=1);

/**
 * @file
 * This file was auto-generated by generate-includes.php and includes all of
 * the core files required by HTML Purifier. Use this if performance is a
 * primary concern and you are using an opcode cache. PLEASE DO NOT EDIT THIS
 * FILE, changes will be overwritten the next time the script is run.
 *
 * @version 4.12.0
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
require 'HTMLPurifier/DefinitionCache.php';
require '../src/DefinitionCacheFactory.php';
require '../src/Doctype.php';
require '../src/DoctypeRegistry.php';
require '../src/ElementDef.php';
require '../src/Encoder.php';
require '../src/EntityLookup.php';
require '../src/EntityParser.php';
require 'HTMLPurifier/ErrorCollector.php';
require 'HTMLPurifier/ErrorStruct.php';
require 'HTMLPurifier/Exception.php';
require '../src/Filter.php';
require 'HTMLPurifier/Generator.php';
require '../src/HTMLDefinition.php';
require 'HTMLPurifier/HTMLModule.php';
require 'HTMLPurifier/HTMLModuleManager.php';
require 'HTMLPurifier/IDAccumulator.php';
require 'HTMLPurifier/Injector.php';
require 'HTMLPurifier/Language.php';
require 'HTMLPurifier/LanguageFactory.php';
require 'HTMLPurifier/Length.php';
require 'HTMLPurifier/Lexer.php';
require '../src/Node.php';
require 'HTMLPurifier/PercentEncoder.php';
require 'HTMLPurifier/PropertyList.php';
require 'HTMLPurifier/PropertyListIterator.php';
require '../src/Queue.php';
require '../src/Strategy.php';
require '../src/StringHash.php';
require '../src/StringHashParser.php';
require 'HTMLPurifier/TagTransform.php';
require '../src/Token.php';
require 'HTMLPurifier/TokenFactory.php';
require '../src/URI.php';
require 'HTMLPurifier/URIDefinition.php';
require '../src/URIFilter.php';
require '../src/URIParser.php';
require 'HTMLPurifier/URIScheme.php';
require 'HTMLPurifier/URISchemeRegistry.php';
require '../src/UnitConverter.php';
require '../src/VarParser.php';
require 'HTMLPurifier/VarParserException.php';
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
require 'HTMLPurifier/AttrTransform/Background.php';
require 'HTMLPurifier/AttrTransform/BdoDir.php';
require 'HTMLPurifier/AttrTransform/BgColor.php';
require 'HTMLPurifier/AttrTransform/BoolToCSS.php';
require 'HTMLPurifier/AttrTransform/Border.php';
require 'HTMLPurifier/AttrTransform/EnumToCSS.php';
require 'HTMLPurifier/AttrTransform/ImgRequired.php';
require 'HTMLPurifier/AttrTransform/ImgSpace.php';
require 'HTMLPurifier/AttrTransform/Input.php';
require 'HTMLPurifier/AttrTransform/Lang.php';
require 'HTMLPurifier/AttrTransform/Length.php';
require 'HTMLPurifier/AttrTransform/Name.php';
require 'HTMLPurifier/AttrTransform/NameSync.php';
require 'HTMLPurifier/AttrTransform/Nofollow.php';
require 'HTMLPurifier/AttrTransform/SafeEmbed.php';
require 'HTMLPurifier/AttrTransform/SafeObject.php';
require 'HTMLPurifier/AttrTransform/SafeParam.php';
require 'HTMLPurifier/AttrTransform/ScriptRequired.php';
require 'HTMLPurifier/AttrTransform/TargetBlank.php';
require 'HTMLPurifier/AttrTransform/TargetNoopener.php';
require 'HTMLPurifier/AttrTransform/TargetNoreferrer.php';
require 'HTMLPurifier/AttrTransform/Textarea.php';
require 'HTMLPurifier/ChildDef/Chameleon.php';
require 'HTMLPurifier/ChildDef/Custom.php';
require 'HTMLPurifier/ChildDef/Empty.php';
require 'HTMLPurifier/ChildDef/List.php';
require 'HTMLPurifier/ChildDef/Required.php';
require 'HTMLPurifier/ChildDef/Optional.php';
require 'HTMLPurifier/ChildDef/StrictBlockquote.php';
require 'HTMLPurifier/ChildDef/Table.php';
require 'HTMLPurifier/DefinitionCache/Decorator.php';
require 'HTMLPurifier/DefinitionCache/Null.php';
require '../src/DefinitionCache/Serializer.php';
require 'HTMLPurifier/DefinitionCache/Decorator/Cleanup.php';
require 'HTMLPurifier/DefinitionCache/Decorator/Memory.php';
require 'HTMLPurifier/HTMLModule/Bdo.php';
require 'HTMLPurifier/HTMLModule/CommonAttributes.php';
require 'HTMLPurifier/HTMLModule/Edit.php';
require 'HTMLPurifier/HTMLModule/Forms.php';
require 'HTMLPurifier/HTMLModule/Hypertext.php';
require 'HTMLPurifier/HTMLModule/Iframe.php';
require 'HTMLPurifier/HTMLModule/Image.php';
require 'HTMLPurifier/HTMLModule/Legacy.php';
require 'HTMLPurifier/HTMLModule/List.php';
require 'HTMLPurifier/HTMLModule/Name.php';
require 'HTMLPurifier/HTMLModule/Nofollow.php';
require 'HTMLPurifier/HTMLModule/NonXMLCommonAttributes.php';
require 'HTMLPurifier/HTMLModule/Object.php';
require 'HTMLPurifier/HTMLModule/Presentation.php';
require 'HTMLPurifier/HTMLModule/Proprietary.php';
require 'HTMLPurifier/HTMLModule/Ruby.php';
require 'HTMLPurifier/HTMLModule/SafeEmbed.php';
require 'HTMLPurifier/HTMLModule/SafeObject.php';
require 'HTMLPurifier/HTMLModule/SafeScripting.php';
require 'HTMLPurifier/HTMLModule/Scripting.php';
require 'HTMLPurifier/HTMLModule/StyleAttribute.php';
require 'HTMLPurifier/HTMLModule/Tables.php';
require 'HTMLPurifier/HTMLModule/Target.php';
require 'HTMLPurifier/HTMLModule/TargetBlank.php';
require 'HTMLPurifier/HTMLModule/TargetNoopener.php';
require 'HTMLPurifier/HTMLModule/TargetNoreferrer.php';
require 'HTMLPurifier/HTMLModule/Text.php';
require 'HTMLPurifier/HTMLModule/Tidy.php';
require 'HTMLPurifier/HTMLModule/XMLCommonAttributes.php';
require 'HTMLPurifier/HTMLModule/Tidy/Name.php';
require 'HTMLPurifier/HTMLModule/Tidy/Proprietary.php';
require 'HTMLPurifier/HTMLModule/Tidy/XHTMLAndHTML4.php';
require 'HTMLPurifier/HTMLModule/Tidy/Strict.php';
require 'HTMLPurifier/HTMLModule/Tidy/Transitional.php';
require 'HTMLPurifier/HTMLModule/Tidy/XHTML.php';
require 'HTMLPurifier/Injector/AutoParagraph.php';
require 'HTMLPurifier/Injector/DisplayLinkURI.php';
require 'HTMLPurifier/Injector/Linkify.php';
require 'HTMLPurifier/Injector/PurifierLinkify.php';
require 'HTMLPurifier/Injector/RemoveEmpty.php';
require 'HTMLPurifier/Injector/RemoveSpansWithoutAttributes.php';
require 'HTMLPurifier/Injector/SafeObject.php';
require '../src/Lexer/DOMLex.php';
require 'HTMLPurifier/Lexer/DirectLex.php';
require '../src/Node/Comment.php';
require '../src/Node/Element.php';
require '../src/Node/Text.php';
require 'HTMLPurifier/Strategy/Composite.php';
require 'HTMLPurifier/Strategy/Core.php';
require 'HTMLPurifier/Strategy/FixNesting.php';
require 'HTMLPurifier/Strategy/MakeWellFormed.php';
require 'HTMLPurifier/Strategy/RemoveForeignElements.php';
require 'HTMLPurifier/Strategy/ValidateAttributes.php';
require 'HTMLPurifier/TagTransform/Font.php';
require 'HTMLPurifier/TagTransform/Simple.php';
require 'HTMLPurifier/Token/Comment.php';
require '../src/Token/Tag.php';
require 'HTMLPurifier/Token/Empty.php';
require '../src/Token/End.php';
require '../src/Token/Start.php';
require 'HTMLPurifier/Token/Text.php';
require 'HTMLPurifier/URIFilter/DisableExternal.php';
require 'HTMLPurifier/URIFilter/DisableExternalResources.php';
require 'HTMLPurifier/URIFilter/DisableResources.php';
require 'HTMLPurifier/URIFilter/HostBlacklist.php';
require 'HTMLPurifier/URIFilter/MakeAbsolute.php';
require 'HTMLPurifier/URIFilter/Munge.php';
require 'HTMLPurifier/URIFilter/SafeIframe.php';
require 'HTMLPurifier/URIScheme/data.php';
require 'HTMLPurifier/URIScheme/file.php';
require 'HTMLPurifier/URIScheme/ftp.php';
require 'HTMLPurifier/URIScheme/http.php';
require 'HTMLPurifier/URIScheme/https.php';
require 'HTMLPurifier/URIScheme/mailto.php';
require 'HTMLPurifier/URIScheme/news.php';
require 'HTMLPurifier/URIScheme/nntp.php';
require 'HTMLPurifier/URIScheme/tel.php';
require 'HTMLPurifier/VarParser/Flexible.php';
require 'HTMLPurifier/VarParser/Native.php';
