<?php

use core\addons\XWAddonImplementation;
use core\modules\factories\XWModuleListFactory;
use core\modules\XWModule;
use core\modules\XWModuleDeployer;
use core\net\XWRequest;
use core\pages\plain\XWPageList;
use core\pages\plain\XWSubPageMenuItem;
use core\pages\XWCallableContent;
use core\utils\XWServerInstanceToolKit;
use xw\entities\users\XWUser;
use xw\entities\users\XWUserDAO;
use core\pages\plain\XWPage;
use core\logging\XWLogger;

class XWMobileMenuTwig extends XWAddonImplementation {
    private $inverseStyle = false;
    private $pageFactoryClassName = "core\pages\plain\XWPageListFactory";
    private $useSEO = true;

    private $searchEnabled = false;
    private $searchTarget = '';
    private $searchFieldname = 'mobilemenu_search';
    private $searchLabel = 'Search';
    private $searchPlaceholder = '';
    
    private function getPageFolder():string {
        return XWServerInstanceToolKit::instance()->getServerSwitch()->getPages();
    }
    
    private function getFullPageList(): XWPageList{
        $result = null;
        try{
            $ref = new ReflectionClass($this->pageFactoryClassName);
            if($ref->hasMethod("getFullPageList")){
                $method = $ref->getMethod("getFullPageList");
                $result = $method->invokeArgs(null, [$this->getPageFolder()]);
            }
        }
        catch(Exception $e){
            $this->getLogger()->log(XWLogger::ERROR, $e->getMessage(), $e);
        }
        return $result;
    }
    
    /**
     * @return XWModule[]
     */
    private function getFullModuleList(): array{
        return XWModuleListFactory::getFullModuleList()->toArrayList()->toArray();
    }
    private function checkModuleRestrictions(XWModule $module){
        $user=new XWUser();
        if(isset($_SESSION["XWUSER"])){
            $user=$_SESSION["XWUSER"];
        }
        return $module->hasUserPermission($user);
    }
    
    /**
     * Extend and Override to implement menus with other sources
     * @param string $parent
     * @return array
     */
    protected function loadPagesStructure($parent = ""):array{
        //['page'=>null,'children'=>null]
        $result = [];
        $list = $this->getFullPageList();
        $list->sortByNameASC();
        for($i=0;$i<$list->getSize();$i++){
            $page = $list->getPage($i);
            if($page->getParentPage() == $parent && !$page->isHidden()){
                $result[] = [
                    'page' => $page,
                    'link' => $this->buildLink($page),
                    'children' => $this->loadPagesStructure($page->getCallName()),
                    'active' => $this->checkActive($page->getCallName()),
                    'parentAsHeader' => false,
                    'name' => $page->getName(),
                ];

                if(count($result[count($result) - 1]['children']) > 0){
                    $result[count($result) - 1]['dropdown'] = 'dropdown';
                }
                else{
                    $result[count($result) - 1]['dropdown'] = null;
                }
            }
        }
        if(strlen($parent) == 0){
            $modules = $this->getFullModuleList();
            /** @var XWModule $module */
            foreach($modules as $module){
                if(!$module->isHidden() && $this->checkModuleRestrictions($module)){
                    $deployer = new XWModuleDeployer();
                    $deployer->load($module);
                    
                    $children = [];
                    for($iM=0;$iM<$deployer->getSize();$iM++){
                        $item = $deployer->getSubPageMenuItem($iM);
                        $children[] = [
                            'page' => $item,
                            'link' => $this->buildLink($item),
                            'children' => $this->loadModuleSubPagesStructure($item),
                            'active' => $this->checkActive($item->getCallName()),
                            'parentAsHeader' => true,
                            'name' => $item->getName(),
                        ];

                        if(count($children[count($children) - 1]['children']) > 0){
                            $children[count($children) - 1]['dropdown'] = 'dropdown';
                        }
                        else{
                            $children[count($children) - 1]['dropdown'] = null;
                        }
                    }
                    
                    $result[] = [
                        'page' => $module,
                        'link' => $this->buildLink($module),
                        'children' => $children,
                        'active' => $this->checkActive($module->getCallName()),
                        'parentAsHeader' => true,
                        'name' => $module->getName(),
                    ];

                    if(count($result[count($result) - 1]['children']) > 0){
                        $result[count($result) - 1]['dropdown'] = 'dropdown';
                    }
                    else{
                        $result[count($result) - 1]['dropdown'] = null;
                    }
                }
            }
        }
        if(strlen($parent) == 0 && XWUserDAO::instance()->getCurrentUser()->isInGroup("admins")){
            $page = new XWPage();
            $page->setName("Admin");
            $page->setCallName("system");
            $page->setAdminPage(true);
            $result[] = [
                'page' => $page,
                'children' => [],
            ];
        }
        return $result;
    }
    
    /**
     * @param XWSubPageMenuItem[] $items
     * @return array
     */
    private function loadModuleSubPagesStructure(XWSubPageMenuItem $items): array{
        $result = [];
        for($i=0;$i<$items->getSize();$i++){
            $result[] = $items->getSubItem($i);
        }
        return $result;
    }
    
    private function getWaypointsContent():string {
        return '
            /*!
            Waypoints - 4.0.1
            Copyright � 2011-2016 Caleb Troughton
            Licensed under the MIT license.
            https://github.com/imakewebthings/waypoints/blob/master/licenses.txt
            */
            !function(){"use strict";function t(n){if(!n)throw new Error("No options passed to Waypoint constructor");if(!n.element)throw new Error("No element option passed to Waypoint constructor");if(!n.handler)throw new Error("No handler option passed to Waypoint constructor");this.key="waypoint-"+e,this.options=t.Adapter.extend({},t.defaults,n),this.element=this.options.element,this.adapter=new t.Adapter(this.element),this.callback=n.handler,this.axis=this.options.horizontal?"horizontal":"vertical",this.enabled=this.options.enabled,this.triggerPoint=null,this.group=t.Group.findOrCreate({name:this.options.group,axis:this.axis}),this.context=t.Context.findOrCreateByElement(this.options.context),t.offsetAliases[this.options.offset]&&(this.options.offset=t.offsetAliases[this.options.offset]),this.group.add(this),this.context.add(this),i[this.key]=this,e+=1}var e=0,i={};t.prototype.queueTrigger=function(t){this.group.queueTrigger(this,t)},t.prototype.trigger=function(t){this.enabled&&this.callback&&this.callback.apply(this,t)},t.prototype.destroy=function(){this.context.remove(this),this.group.remove(this),delete i[this.key]},t.prototype.disable=function(){return this.enabled=!1,this},t.prototype.enable=function(){return this.context.refresh(),this.enabled=!0,this},t.prototype.next=function(){return this.group.next(this)},t.prototype.previous=function(){return this.group.previous(this)},t.invokeAll=function(t){var e=[];for(var n in i)e.push(i[n]);for(var o=0,r=e.length;r>o;o++)e[o][t]()},t.destroyAll=function(){t.invokeAll("destroy")},t.disableAll=function(){t.invokeAll("disable")},t.enableAll=function(){t.Context.refreshAll();for(var e in i)i[e].enabled=!0;return this},t.refreshAll=function(){t.Context.refreshAll()},t.viewportHeight=function(){return window.innerHeight||document.documentElement.clientHeight},t.viewportWidth=function(){return document.documentElement.clientWidth},t.adapters=[],t.defaults={context:window,continuous:!0,enabled:!0,group:"default",horizontal:!1,offset:0},t.offsetAliases={"bottom-in-view":function(){return this.context.innerHeight()-this.adapter.outerHeight()},"right-in-view":function(){return this.context.innerWidth()-this.adapter.outerWidth()}},window.Waypoint=t}(),function(){"use strict";function t(t){window.setTimeout(t,1e3/60)}function e(t){this.element=t,this.Adapter=o.Adapter,this.adapter=new this.Adapter(t),this.key="waypoint-context-"+i,this.didScroll=!1,this.didResize=!1,this.oldScroll={x:this.adapter.scrollLeft(),y:this.adapter.scrollTop()},this.waypoints={vertical:{},horizontal:{}},t.waypointContextKey=this.key,n[t.waypointContextKey]=this,i+=1,o.windowContext||(o.windowContext=!0,o.windowContext=new e(window)),this.createThrottledScrollHandler(),this.createThrottledResizeHandler()}var i=0,n={},o=window.Waypoint,r=window.onload;e.prototype.add=function(t){var e=t.options.horizontal?"horizontal":"vertical";this.waypoints[e][t.key]=t,this.refresh()},e.prototype.checkEmpty=function(){var t=this.Adapter.isEmptyObject(this.waypoints.horizontal),e=this.Adapter.isEmptyObject(this.waypoints.vertical),i=this.element==this.element.window;t&&e&&!i&&(this.adapter.off(".waypoints"),delete n[this.key])},e.prototype.createThrottledResizeHandler=function(){function t(){e.handleResize(),e.didResize=!1}var e=this;this.adapter.on("resize.waypoints",function(){e.didResize||(e.didResize=!0,o.requestAnimationFrame(t))})},e.prototype.createThrottledScrollHandler=function(){function t(){e.handleScroll(),e.didScroll=!1}var e=this;this.adapter.on("scroll.waypoints",function(){(!e.didScroll||o.isTouch)&&(e.didScroll=!0,o.requestAnimationFrame(t))})},e.prototype.handleResize=function(){o.Context.refreshAll()},e.prototype.handleScroll=function(){var t={},e={horizontal:{newScroll:this.adapter.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.adapter.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};for(var i in e){var n=e[i],o=n.newScroll>n.oldScroll,r=o?n.forward:n.backward;for(var s in this.waypoints[i]){var l=this.waypoints[i][s];if(null!==l.triggerPoint){var a=n.oldScroll<l.triggerPoint,h=n.newScroll>=l.triggerPoint,p=a&&h,u=!a&&!h;(p||u)&&(l.queueTrigger(r),t[l.group.id]=l.group)}}}for(var d in t)t[d].flushTriggers();this.oldScroll={x:e.horizontal.newScroll,y:e.vertical.newScroll}},e.prototype.innerHeight=function(){return this.element==this.element.window?o.viewportHeight():this.adapter.innerHeight()},e.prototype.remove=function(t){delete this.waypoints[t.axis][t.key],this.checkEmpty()},e.prototype.innerWidth=function(){return this.element==this.element.window?o.viewportWidth():this.adapter.innerWidth()},e.prototype.destroy=function(){var t=[];for(var e in this.waypoints)for(var i in this.waypoints[e])t.push(this.waypoints[e][i]);for(var n=0,o=t.length;o>n;n++)t[n].destroy()},e.prototype.refresh=function(){var t,e=this.element==this.element.window,i=e?void 0:this.adapter.offset(),n={};this.handleScroll(),t={horizontal:{contextOffset:e?0:i.left,contextScroll:e?0:this.oldScroll.x,contextDimension:this.innerWidth(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:e?0:i.top,contextScroll:e?0:this.oldScroll.y,contextDimension:this.innerHeight(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};for(var r in t){var s=t[r];for(var l in this.waypoints[r]){var a,h,p,u,d,f=this.waypoints[r][l],c=f.options.offset,w=f.triggerPoint,y=0,g=null==w;f.element!==f.element.window&&(y=f.adapter.offset()[s.offsetProp]),"function"==typeof c?c=c.apply(f):"string"==typeof c&&(c=parseFloat(c),f.options.offset.indexOf("%")>-1&&(c=Math.ceil(s.contextDimension*c/100))),a=s.contextScroll-s.contextOffset,f.triggerPoint=Math.floor(y+a-c),h=w<s.oldScroll,p=f.triggerPoint>=s.oldScroll,u=h&&p,d=!h&&!p,!g&&u?(f.queueTrigger(s.backward),n[f.group.id]=f.group):!g&&d?(f.queueTrigger(s.forward),n[f.group.id]=f.group):g&&s.oldScroll>=f.triggerPoint&&(f.queueTrigger(s.forward),n[f.group.id]=f.group)}}return o.requestAnimationFrame(function(){for(var t in n)n[t].flushTriggers()}),this},e.findOrCreateByElement=function(t){return e.findByElement(t)||new e(t)},e.refreshAll=function(){for(var t in n)n[t].refresh()},e.findByElement=function(t){return n[t.waypointContextKey]},window.onload=function(){r&&r(),e.refreshAll()},o.requestAnimationFrame=function(e){var i=window.requestAnimationFrame||window.mozRequestAnimationFrame||window.webkitRequestAnimationFrame||t;i.call(window,e)},o.Context=e}(),function(){"use strict";function t(t,e){return t.triggerPoint-e.triggerPoint}function e(t,e){return e.triggerPoint-t.triggerPoint}function i(t){this.name=t.name,this.axis=t.axis,this.id=this.name+"-"+this.axis,this.waypoints=[],this.clearTriggerQueues(),n[this.axis][this.name]=this}var n={vertical:{},horizontal:{}},o=window.Waypoint;i.prototype.add=function(t){this.waypoints.push(t)},i.prototype.clearTriggerQueues=function(){this.triggerQueues={up:[],down:[],left:[],right:[]}},i.prototype.flushTriggers=function(){for(var i in this.triggerQueues){var n=this.triggerQueues[i],o="up"===i||"left"===i;n.sort(o?e:t);for(var r=0,s=n.length;s>r;r+=1){var l=n[r];(l.options.continuous||r===n.length-1)&&l.trigger([i])}}this.clearTriggerQueues()},i.prototype.next=function(e){this.waypoints.sort(t);var i=o.Adapter.inArray(e,this.waypoints),n=i===this.waypoints.length-1;return n?null:this.waypoints[i+1]},i.prototype.previous=function(e){this.waypoints.sort(t);var i=o.Adapter.inArray(e,this.waypoints);return i?this.waypoints[i-1]:null},i.prototype.queueTrigger=function(t,e){this.triggerQueues[e].push(t)},i.prototype.remove=function(t){var e=o.Adapter.inArray(t,this.waypoints);e>-1&&this.waypoints.splice(e,1)},i.prototype.first=function(){return this.waypoints[0]},i.prototype.last=function(){return this.waypoints[this.waypoints.length-1]},i.findOrCreate=function(t){return n[t.axis][t.name]||new i(t)},o.Group=i}(),function(){"use strict";function t(t){return t===t.window}function e(e){return t(e)?e:e.defaultView}function i(t){this.element=t,this.handlers={}}var n=window.Waypoint;i.prototype.innerHeight=function(){var e=t(this.element);return e?this.element.innerHeight:this.element.clientHeight},i.prototype.innerWidth=function(){var e=t(this.element);return e?this.element.innerWidth:this.element.clientWidth},i.prototype.off=function(t,e){function i(t,e,i){for(var n=0,o=e.length-1;o>n;n++){var r=e[n];i&&i!==r||t.removeEventListener(r)}}var n=t.split("."),o=n[0],r=n[1],s=this.element;if(r&&this.handlers[r]&&o)i(s,this.handlers[r][o],e),this.handlers[r][o]=[];else if(o)for(var l in this.handlers)i(s,this.handlers[l][o]||[],e),this.handlers[l][o]=[];else if(r&&this.handlers[r]){for(var a in this.handlers[r])i(s,this.handlers[r][a],e);this.handlers[r]={}}},i.prototype.offset=function(){if(!this.element.ownerDocument)return null;var t=this.element.ownerDocument.documentElement,i=e(this.element.ownerDocument),n={top:0,left:0};return this.element.getBoundingClientRect&&(n=this.element.getBoundingClientRect()),{top:n.top+i.pageYOffset-t.clientTop,left:n.left+i.pageXOffset-t.clientLeft}},i.prototype.on=function(t,e){var i=t.split("."),n=i[0],o=i[1]||"__default",r=this.handlers[o]=this.handlers[o]||{},s=r[n]=r[n]||[];s.push(e),this.element.addEventListener(n,e)},i.prototype.outerHeight=function(e){var i,n=this.innerHeight();return e&&!t(this.element)&&(i=window.getComputedStyle(this.element),n+=parseInt(i.marginTop,10),n+=parseInt(i.marginBottom,10)),n},i.prototype.outerWidth=function(e){var i,n=this.innerWidth();return e&&!t(this.element)&&(i=window.getComputedStyle(this.element),n+=parseInt(i.marginLeft,10),n+=parseInt(i.marginRight,10)),n},i.prototype.scrollLeft=function(){var t=e(this.element);return t?t.pageXOffset:this.element.scrollLeft},i.prototype.scrollTop=function(){var t=e(this.element);return t?t.pageYOffset:this.element.scrollTop},i.extend=function(){function t(t,e){if("object"==typeof t&&"object"==typeof e)for(var i in e)e.hasOwnProperty(i)&&(t[i]=e[i]);return t}for(var e=Array.prototype.slice.call(arguments),i=1,n=e.length;n>i;i++)t(e[0],e[i]);return e[0]},i.inArray=function(t,e,i){return null==e?-1:e.indexOf(t,i)},i.isEmptyObject=function(t){for(var e in t)return!1;return!0},n.adapters.push({name:"noframework",Adapter:i}),n.Adapter=i}();
        ';
    }
    
    private function getJSContent():string {
        return '
            var waypoint = new Waypoint({
                element: document.getElementById("mobilemenu-notfixed"),
                handler: function(direction) {
                    
                    if($("nav#mobilemenu-notfixed").hasClass("navbar-fixed-top")){
                        $("nav#mobilemenu-notfixed").removeClass("navbar-fixed-top")
                    }
                    else{
                        $("nav#mobilemenu-notfixed").addClass("navbar-fixed-top")
                    }
                }
            });
            document.getElementById("mobilemenu-fixed").style.display = "none";
        '."\n";
    }
    
    private function checkActive(string $pageName):string {
        return XWRequest::instance()->exists("page") && XWRequest::instance()->get("page") == $pageName ? 'active' : '';
    }
    
    protected function buildLink(XWCallableContent $content):string{
        if($content->getRedirectLink()){
            return $content->getRedirectLink();
        }

        if(strlen($content->getParent()) > 0 && ($content instanceof XWModule || $content instanceof  XWSubPageMenuItem)){
            return "index.php?page=".$content->getParent()."&sub=" . $content->getCallName();
        }
        else if ($content instanceof XWPage && $content->isAdminPage()){
            return "index.php?adminpage=1&page=" . $content->getCallName();
        }

        if($this->useSEO){
            if($content->getParent()){
                return '/' . $content->getParent() . '/' . $content->getCallName() . '.html';
            }
            else{
                return '/' . $content->getCallName() . '.html';
            }
        }
        else{
            return "index.php?page=" . $content->getCallName();
        }
    }
    
    public function getMenu(bool $fixed = false):string {
        $model = [];
        $model['style'] = $this->inverseStyle ? 'navbar-inverse' : 'navbar-noninverse';
        $model['type'] = $fixed ? 'navbar-fixed-top' : '';
        $model['id'] = $fixed ? 'mobilemenu-fixed' : 'mobilemenu-notfixed';
        $model['homelink'] = 'index.php';
        $model['items'] = $this->loadPagesStructure();
        $model['env'] = XWServerInstanceToolKit::instance()->getEnvValues();

        $model['search'] = [
            'enabled' => $this->searchEnabled,
            'target' => $this->searchTarget,
            'fieldname' => $this->searchFieldname,
            'placeholder' => $this->searchPlaceholder,
            'buttonLabel' => $this->searchLabel,
        ];

        $result = '';

        try{
            $result = $this->renderTemplate('mobilemenu.twig', $model);
        }
        catch(Exception $e){
            $this->getLogger()->log(XWLogger::ERROR, $e->getMessage(), $e);
        }

        return $result;
    }
    
    public function getToogleMenu(): string{
        $result = '<script type="text/javascript">';
        $result .= $this->getWaypointsContent();
        $result .= '</script>'."\n\n";
        
        $result .= $this->getMenu(false);
        $result .= "\n";
        
        $result .= '<script type="text/javascript">';
        $result .= $this->getJSContent();
        $result .= '</script>'."\n\n";
        
        return $result;
    }

    public function render($vars = []): string
    {
        if($vars['fixed']) {
            return $this->getMenu(true);
        }
        else {
            return $this->getToogleMenu();
        }
    }

    /**
     * @return bool
     */
    public function isSearchEnabled(): bool
    {
        return $this->searchEnabled;
    }

    /**
     * @param bool $searchEnabled
     */
    public function setSearchEnabled(bool $searchEnabled): void
    {
        $this->searchEnabled = $searchEnabled;
    }

    /**
     * @return string
     */
    public function getSearchTarget(): string
    {
        return $this->searchTarget;
    }

    /**
     * @param string $searchTarget
     */
    public function setSearchTarget(string $searchTarget): void
    {
        $this->searchTarget = $searchTarget;
    }

    /**
     * @return string
     */
    public function getSearchFieldname(): string
    {
        return $this->searchFieldname;
    }

    /**
     * @param string $searchFieldname
     */
    public function setSearchFieldname(string $searchFieldname): void
    {
        $this->searchFieldname = $searchFieldname;
    }

    /**
     * @return string
     */
    public function getSearchLabel(): string
    {
        return $this->searchLabel;
    }

    /**
     * @param string $searchLabel
     */
    public function setSearchLabel(string $searchLabel): void
    {
        $this->searchLabel = $searchLabel;
    }

    /**
     * @return string
     */
    public function getSearchPlaceholder(): string
    {
        return $this->searchPlaceholder;
    }

    /**
     * @param string $searchPlaceholder
     */
    public function setSearchPlaceholder(string $searchPlaceholder): void
    {
        $this->searchPlaceholder = $searchPlaceholder;
    }
}