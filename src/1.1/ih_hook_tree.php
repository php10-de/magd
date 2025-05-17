<?php
$modul = "ih_hook";

require("inc/req.php");

/* * * Rights ** */
// Generally for people with the right to view ih_repo
$groupID = 29;
GRGR($groupID);

if (!$headless)
    require("inc/header.inc.php");
?>


<style>
    .content {
        width: 100%
    }

    .main {
        width: 100%;
    }
</style>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

<!-- replace:css -->
<!--<link rel="stylesheet" href="<?php echo HTTP_SUB ?>assets/vendor/angular-ui-notification/angular-ui-notification.min.css" />-->
<!-- /replace:css -->

<!-- /Style Libraries -->

<!-- Style Custom -->
<!--<link rel="stylesheet" href="https://hrose.eu/ih_hook_tree_style.css" type="text/css" />-->
<!-- /Style Custom -->

<!-- replace:js -->
<!-- Script libraries -->
<script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/vendor/angularjs/angular.min.js"></script>
<script src="<?php echo HTTP_SUB ?>assets/vendor/angular-ui-tree/angular-ui-tree.min.js"></script>
<!--<script src="<?php echo HTTP_SUB ?>assets/vendor/angular-ui-notification/angular-ui-notification.min.js"></script>-->
<!-- /Script Libraries -->

<!-- Script Custom -->
<script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/angular/services/request.service.js"></script>
<script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/angular/services/hooks.service.js"></script>
<script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/angular/app.js"></script>
<!-- /Script Custom -->
<!-- /replace:js -->

<header class="page-header">

    <h2>
        Repository:
        <a href="branch.php?repo=<?php echo $_SESSION['repo'] ?>">
            <span>
                <?php echo $_SESSION['ih_branch']['ih_repo_id'] ?>
            </span>
        </a>
        <? if(isset($_SESSION['ih_branch']['name'])){ ?>
        <span>
            <?php echo "=> " . $_REQUEST['ih_branch_id'] ?>
        </span>
        <? } ?>
    </h2>

    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="index.html">
                    <i class="fa fa-home"></i>
                </a>
            </li>
            <li><span>Pages</span></li>
            <li><span>Infinite Hooks</span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>

<!-- start: page -->
<section class="content-with-menu content-with-menu-has-toolbar media-gallery" ng-app="redis" ng-controller="RedisCtrl">
    <div class="content-with-menu-container" ng-cloak>
        <div class="inner-menu-toggle">
            <a href="#" class="inner-menu-expand" data-open="inner-menu">
                Show Bar <i class="fa fa-chevron-right"></i>
            </a>
        </div>

        <menu id="content-menu" class="inner-menu" role="menu">
            <div class="nano">
                <div class="nano-content">

                    <div class="inner-menu-toggle-inside">
                        <a href="#" class="inner-menu-collapse">
                            <i class="fa fa-chevron-up visible-xs-inline"></i><i class="fa fa-chevron-left hidden-xs-inline"></i> Hide Bar
                        </a>
                        <a href="#" class="inner-menu-expand" data-open="inner-menu">
                            Show Bar <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>

                    <div class="inner-menu-content">

<!--                        <input class="form-control input-md mb-md" type="text" placeholder="Search..."/>-->

                        <hr class="separator" />

                        <div class="sidebar-widget m-none">
                            <div class="widget-header clearfix">
                                <h6 class="title pull-left mt-xs">Files</h6>
                                <div class="pull-right">
<!--                                    <a href="#" class="btn btn-dark btn-sm btn-widget-act">Add Folder</a>-->
                                </div>
                            </div>
                            <div class="widget-content">


                                <div id="smallbrowser">

                                    <!-- Nested node template -->
                                    <script type="text/ng-template" id="test.html">
                                        <div class=" ui-tree-element-content" ng-class="{ 'jstree-leaf': (!node.have_subdirs), 'jstree-closed': ( ( collapsed === true ) && node.have_subdirs),'jstree-open': ( (collapsed === false) && node.have_subdirs)}" ui-tree-handle="">
                                            <!-- Button Only for Home -->
                                            <i class="jstree-icon jstree-ocl" ng-click="toggle(this)" ng-if="node.nodes && node.nodes.length >= 0 && !loading" data-nodrag  ></i>

                                            <!-- Button For all folders  -->
                                            <i class="jstree-icon jstree-ocl" ng-click="expand_tree_item($nodeScope, this)"  ng-if="node.have_subdirs && !node.nodes && !loading" data-nodrag ></i>

                                            <!-- Button For all files -->
                                            <i class="jstree-icon jstree-ocl" ng-if="!node.have_subdirs && !node.nodes && !loading" data-nodrag ></i>

                                            <!-- Loading Spinner -->
                                            <i class="fa fa-spinner fa-spin fa-fw" ng-if="loading || node.loading" data-nodrag  style="width: 24px;height: 24px"></i>

                                            <!-- List Item Text and unique Icon -->
                                            <a  class="jstree-anchor ui-tree-element-content"
                                                ui-tree-handle=""
                                                data-nodrag
                                                href="#"
                                                ui-tree-element-content
                                                ng-click="click_tree_item(node)"
                                                ng-class="{'jstree-clicked':(node.$$hashKey === selectedFile.$$hashKey) }" >
                                                <i class="jstree-icon jstree-themeicon fa fa-folder jstree-themeicon-custom" ng-if="node.have_subdirs"></i>
                                                <i class="jstree-icon jstree-themeicon fa fa-file jstree-themeicon-custom" ng-if="!node.have_subdirs"></i>
                                                    <span class="icon_wrapper" ng-if="node.hook_contains || node.in_array">
                                                        <i class="hooks_icon"></i>
                                                    </span>
                                                    <span ng-class="{'text-info':(node.hook_contains || node.in_array)}">{{node.file}}</span>
                                            </a>
                                        </div>
                                        <ul ui-tree-nodes="" ng-model="node.nodes" ng-class="{'hide': collapsed}" class="jstree-children">
                                           <li ng-repeat="node in node.nodes" ui-tree-node ng-include="'test.html'" class="jstree-node" data-collapsed="true" ng-class="{'jstree-last':$last}">
                                           </li>
                                        </ul>

                                    </script>

                                    <div ng-if="!filesTreeLoaded" class="loader">
                                        <i class="fa fa-spinner fa-spin" style="font-size:30px;color:#ccc"></i>
                                    </div>

                                    <div class="jstree jstree-1 jstree-default" ui-tree="" data-drag-enabled="false" ng-if="filesTreeLoaded">
                                        <ul class="jstree-container-ul jstree-children" ui-tree-nodes="" ng-model="files" id="tree-root">
                                            <li class="jstree-node  jstree-open jstree-last"
                                                ng-repeat="node in files"
                                                ui-tree-node
                                                ng-include="'test.html'"
                                                data-collapsed="(node.files = 'Home' ? 'false' : true)"
                                                >
                                            </li>
                                        </ul>
                                    </div>

                                </div>





                            </div>
                        </div>

                        <hr class="separator" />

<!--                        <div class="sidebar-widget m-none">
                            <div class="widget-header">
                                <h6 class="title">Labels</h6>
                                <span class="widget-toggle">+</span>
                            </div>
                            <div class="widget-content">
                            </div>
                        </div>-->
                    </div>
                </div>
            </div>
        </menu>
        <div class="inner-body mg-main">

            <div class="inner-toolbar clearfix">
                <ul>
                    <li>
                        <a href="#"><i class="fa fa-refresh"></i> <span>Refresh Data</span></a>
                    </li>
                </ul>
            </div>
            <div>

                <section class="panel panel-featured panel-featured-info">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="fa fa-caret-down"></a>

                        </div>

                        <h2 class="panel-title">My hooks</h2>
                    </header>
                    <div class="panel-body">
                        <table class="table table-no-more table-bordered table-striped mb-none" ng-if="hooks.length > 0 && hooksLoaded === true">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Action</th>
                                    <th class="text-center">Commit</th>
                                    <th class="text-left">File</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="hook in hooks track by $index">
                                    <td data-title="#" class="text-center">{{$index + 1}}</td>
                                    <td data-title="Action" class="text-center">
                                        <button
                                            tooltip
                                            data-toggle="tooltip" data-placement="right" title="Delete"
                                            class="btn btn-danger btn-sm"
                                            ng-click="uninfinitate(hook)"><i class="fa fa-trash-o"></i></button>
                                    </td>
                                    <td data-title="Commit" class="text-center">{{hook.commit.id}}</td>
                                    <td data-title="File" class="text-left"><a href="{{hook.commit.link}}" target="_blank">{{hook.file}}</a></td>
                                </tr>
                            </tbody>
                        </table>
                        <div ng-if="hooks.length === 0 && hooksLoaded === true" class="m-5 text-center">
                            No hooks found.
                        </div>
                        <div ng-if="hooksLoaded === false" class="m-5 text-center">
                            <i class="fa fa-spinner fa-spin" style="font-size:30px;color:#ccc"></i>
                        </div>
                    </div>
                </section>

                <section class="panel panel-featured panel-featured-primary">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="#" class="fa fa-caret-down"></a>

                        </div>

                        <h2 class="panel-title">My commits</h2>
                    </header>
                    <div class="panel-body">
                        <table class="table table-no-more table-bordered table-striped mb-none" ng-if="view_commits.length > 0 && commitsLoaded === true">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Action</th>
                                    <th class="text-center">Commit</th>
                                    <th class="text-center">Infinitated</th>
                                    <th class="text-left">Info</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="vcommit in view_commits track by $index">
                                    <td data-title="#" class="text-center">{{$index + 1}}</td>
                                    <td data-title="Action" class="text-center">
                                        <button
                                            tooltip
                                            data-toggle="tooltip" data-placement="right" title="Infinitate"
                                            class="btn btn-success btn-sm"
                                            ng-disabled="vcommit.none"
                                            ng-click="infinitate(vcommit)">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </td>
                                    <td data-title="Commit" class="text-center">
                                        <a href="{{vcommit.commit.link}}" target="_blank">{{vcommit.commit.id}}</a>
                                    </td>
                                    <td data-title="Infinitated" class="text-center">
                                        <input type="checkbox" style="height:20px;width:20px;" class="" ng-model="vcommit.none" ng-disabled="true" />
                                    </td>
                                    <td data-title="Info" class="text-left">
                                        {{vcommit.commit.description}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div ng-if="(view_commits.length === 0 && commitsLoaded === true) || commitsLoaded === null" class="m-5 text-center">
                            No Commits found.
                        </div>
                        <div ng-if="commitsLoaded === false" class="m-5 text-center">
                            <i class="fa fa-spinner fa-spin" style="font-size:30px;color:#ccc"></i>
                        </div>
                    </div>
                </section>
                <script type="text/javascript">
                    window.IH = {};
                    window.IH.company_git = <?php echo "'" . $company_git . "'" ?>;
                    window.IH.branch_id = '<?php echo $_REQUEST['ih_branch_id']; ?>';
                    window.IH.repo_id = '<?php echo $_SESSION['ih_branch']['ih_repo_id']; ?>'
                </script>
                <script type="text/javascript" src="https://infinitehooks.com/js/main.min.js"></script>
            </div>
        </div>
    </div>
</section>
<!-- end: page -->

<?php require 'inc/footer.inc.php';?>