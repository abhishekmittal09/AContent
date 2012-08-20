var fluid_1_4=fluid_1_4||{};(function($,fluid){fluid.registerNamespace("fluid.browser.version");fluid.browser.msie=function(){var isIE=($.browser.msie);return isIE?fluid.typeTag("fluid.browser.msie"):undefined};fluid.browser.majorVersion=function(){var version=$.browser.version;var dotpos=version.indexOf(".");var majorVersion=version.substring(0,dotpos);return fluid.typeTag("fluid.browser.majorVersion."+majorVersion)};var features={browserIE:fluid.browser.msie(),browserMajorVersion:fluid.browser.majorVersion()};fluid.merge(null,fluid.staticEnvironment,features);fluid.hasFeature=function(tagName){return fluid.find(fluid.staticEnvironment,function(value){return value&&value.typeName===tagName?true:undefined})};var isTrue=function(val){return val&&(val===true||val==="true")};var setToc=function(that,tocSetting){if(isTrue(tocSetting)){if(that.tableOfContents){that.tableOfContents.show()}else{$(document).ready(that.events.onCreateTOCReady.fire)}}else{if(that.tableOfContents){that.tableOfContents.hide()}}};fluid.defaults("fluid.uiEnhancer",{gradeNames:["fluid.viewComponent","autoInit"],components:{textSize:{type:"fluid.uiEnhancer.textSizer",container:"{uiEnhancer}.container",options:{invokers:{calcInitSize:{funcName:"fluid.uiEnhancer.textSizer.calcInitSize",args:["{textSizer}","{uiEnhancer}.options.fontSizeMap","{uiEnhancer}.options.px2emFactor"]}}}},tableOfContents:{type:"fluid.tableOfContents",container:"{uiEnhancer}.container",createOnEvent:"onCreateTOCReady",options:{components:{levels:{type:"fluid.tableOfContents.levels",options:{resources:{template:{forceCache:true,url:"{uiEnhancer}.options.tocTemplate"}}}}}}},textFont:{type:"fluid.uiEnhancer.classSwapper",container:"{uiEnhancer}.container",options:{classes:"{uiEnhancer}.options.classnameMap.textFont"}},lineSpacing:{type:"fluid.uiEnhancer.lineSpacer",container:"{uiEnhancer}.container",options:{invokers:{calcInitSize:{funcName:"fluid.uiEnhancer.lineSpacer.calcInitSize",args:["{lineSpacer}","{uiEnhancer}.options.fontSizeMap"]}}}},theme:{type:"fluid.uiEnhancer.classSwapper",container:"{uiEnhancer}.container",options:{classes:"{uiEnhancer}.options.classnameMap.theme"}},settingsStore:{type:"fluid.uiOptions.store",options:{defaultSiteSettings:"{uiEnhancer}.options.defaultSiteSettings"}}},invokers:{updateModel:{funcName:"fluid.uiEnhancer.updateModel",args:["@0","{uiEnhancer}.applier"]},updateFromSettingsStore:{funcName:"fluid.uiEnhancer.updateFromSettingsStore",args:["{uiEnhancer}"]},refreshView:{funcName:"fluid.uiEnhancer.refreshView",args:["{uiEnhancer}"]},styleElements:"fluid.uiEnhancer.styleElements",setLayout:"fluid.uiEnhancer.setLayout",styleLinks:"fluid.uiEnhancer.styleLinks",styleInputs:"fluid.uiEnhancer.styleInputs",setIE6ColorInversion:"fluid.uiEnhancer.setIE6ColorInversion"},events:{onCreateTOCReady:null,modelChanged:null},classnameMap:{textFont:{"default":"",times:"fl-font-uio-times",comic:"fl-font-uio-comic-sans",arial:"fl-font-uio-arial",verdana:"fl-font-uio-verdana"},theme:{"default":"fl-uio-default-theme",bw:"fl-theme-uio-bw fl-theme-bw",wb:"fl-theme-uio-wb fl-theme-wb",by:"fl-theme-uio-by fl-theme-by",yb:"fl-theme-uio-yb fl-theme-yb"},layout:"fl-layout-linear",links:"fl-text-underline fl-text-bold fl-text-larger",inputsLarger:"fl-text-larger"},fontSizeMap:{"xx-small":"9px","x-small":"11px",small:"13px",medium:"15px",large:"18px","x-large":"23px","xx-large":"30px"},px2emFactor:"16",selectors:{colorInversion:".fl-inverted-color"},styles:{colorInversionClass:"fl-inverted-color"},finalInitFunction:"fluid.uiEnhancer.finalInit"});fluid.uiEnhancer.finalInit=function(that){that.applier.modelChanged.addListener("",function(newModel,oldModel,changeRequest){that.events.modelChanged.fire(newModel,oldModel,changeRequest);that.refreshView()});that.updateFromSettingsStore();return that};fluid.uiEnhancer.updateFromSettingsStore=function(that){that.updateModel(that.settingsStore.fetch())};fluid.uiEnhancer.updateModel=function(newModel,applier){applier.requestChange("",newModel)};fluid.uiEnhancer.refreshView=function(that){that.textSize.set(that.model.textSize);that.textFont.swap(that.model.textFont);that.lineSpacing.set(that.model.lineSpacing);that.theme.swap(that.model.theme);that.setLayout(that);setToc(that,that.model.toc);that.styleLinks(that);that.styleInputs(that);that.setIE6ColorInversion(that)};fluid.uiEnhancer.styleElements=function(elements,setting,classname){if(setting){elements.addClass(classname)}else{$("."+classname,elements).andSelf().removeClass(classname)}};fluid.uiEnhancer.setLayout=function(that){that.styleElements(that.container,that.model.layout,that.options.classnameMap.layout)};fluid.uiEnhancer.styleLinks=function(that){var links=$("a",that.container);that.styleElements(links,that.model.links,that.options.classnameMap.links)};fluid.uiEnhancer.styleInputs=function(that){that.styleElements($("input, button",that.container),that.model.inputsLarger,that.options.classnameMap.inputsLarger)};fluid.uiEnhancer.setIE6ColorInversion=function(that){if(fluid.hasFeature("fluid.browser.msie")&&fluid.hasFeature("fluid.browser.majorVersion.6")&&that.model.theme==="default"){that.locate("colorInversion").removeClass(that.options.styles.colorInversionClass)}};fluid.uiEnhancer.getTextSizeInPx=function(container,fontSizeMap){var fontSize=container.css("font-size");if(fontSizeMap[fontSize]){fontSize=fontSizeMap[fontSize]}return parseFloat(fontSize)};fluid.uiEnhancer.getTextSizeInEm=function(container,fontSizeMap,px2emFactor){return Math.round(fluid.uiEnhancer.getTextSizeInPx(container,fontSizeMap)/px2emFactor*10000)/10000};fluid.defaults("fluid.uiEnhancer.textSizer",{gradeNames:["fluid.viewComponent","autoInit"],invokers:{set:{funcName:"fluid.uiEnhancer.textSizer.set",args:["@0","{textSizer}"]}}});fluid.uiEnhancer.textSizer.set=function(times,that){if(!that.initialSize){that.calcInitSize()}if(times===1){that.container.css("font-size","")}else{if(times&&times>0){var targetSize=that.initialSize*times+"em";that.container.css("font-size",targetSize)}}};fluid.uiEnhancer.textSizer.calcInitSize=function(that,fontSizeMap,px2emFactor){that.initialSize=fluid.uiEnhancer.getTextSizeInEm(that.container,fontSizeMap,px2emFactor)};fluid.defaults("fluid.uiEnhancer.classSwapper",{gradeNames:["fluid.viewComponent","autoInit"],invokers:{clearClasses:{funcName:"fluid.uiEnhancer.classSwapper.clearClasses",args:["{classSwapper}"]},swap:{funcName:"fluid.uiEnhancer.classSwapper.swap",args:["@0","{classSwapper}"]}},classes:{},finalInitFunction:"fluid.uiEnhancer.classSwapper.finalInit"});fluid.uiEnhancer.classSwapper.finalInit=function(that){that.classSelector="";that.classStr="";fluid.each(that.options.classes,function(className){if(className){that.classSelector+=that.classSelector?", ."+className:"."+className;that.classStr+=that.classStr?" "+className:className}})};fluid.uiEnhancer.classSwapper.clearClasses=function(that){that.container.removeClass(that.classStr)};fluid.uiEnhancer.classSwapper.swap=function(classname,that){that.clearClasses(that);that.container.addClass(that.options.classes[classname])};fluid.defaults("fluid.uiEnhancer.lineSpacer",{gradeNames:["fluid.viewComponent","autoInit"],invokers:{set:{funcName:"fluid.uiEnhancer.lineSpacer.set",args:["@0","{lineSpacer}"]}}});fluid.uiEnhancer.lineSpacer.set=function(times,that){if(!that.initialSize){that.calcInitSize()}var newLineSpacing=times===""||times===1?that.initialSize:times*that.initialSize;that.container.css("line-height",newLineSpacing+"em")};fluid.uiEnhancer.lineSpacer.calcInitSize=function(that,fontSizeMap){var lineHeight=that.container.css("lineHeight");if(lineHeight==="normal"){return 1}if($.browser.msie){var lineHeightInIE;lineHeightInIE=that.container[0].currentStyle.lineHeight;if(lineHeightInIE.match(/[0-9]$/)){that.initialSize=lineHeightInIE;return }}that.initialSize=Math.round(parseFloat(lineHeight)/fluid.uiEnhancer.getTextSizeInPx(that.container,fontSizeMap)*100)/100};fluid.pageEnhancer=function(uiEnhancerOptions){var that=fluid.initLittleComponent("fluid.pageEnhancer");uiEnhancerOptions=fluid.copy(uiEnhancerOptions);uiEnhancerOptions.originalUserOptions=fluid.copy(uiEnhancerOptions);that.uiEnhancerOptions=uiEnhancerOptions;fluid.initDependents(that);fluid.staticEnvironment.uiEnhancer=that.uiEnhancer;return that};fluid.defaults("fluid.pageEnhancer",{gradeNames:["fluid.littleComponent"],components:{uiEnhancer:{type:"fluid.uiEnhancer",container:"body",options:"{pageEnhancer}.uiEnhancerOptions"}}});fluid.demands("fluid.uiOptions.store",["fluid.uiEnhancer"],{funcName:"fluid.cookieStore"})})(jQuery,fluid_1_4);