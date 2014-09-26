# AOE Layout Manager

## Description

This module allows advanced users (with some Magento knowledge) to add layout xml via records in the Magento backend.

Author: Manish Jain

## Installation

Please remember using the `--recursive` parameter while cloning:

    git clone --recursive https://github.com/AOEpeople/Aoe_LayoutManager.git Aoe_LayoutManager

The module comes with a modman configuration file.

## Usage

Modify layout without deploying code (e.g. for temporary hotfixes, or when developer with no Magento access need to add
something like Javascript for a new service)
Modify layout for a given time span

## How it works
Install the module, and clear the cache. Once AOE Layout Manager module will be installed, menu will be appear in Admin -> System -> AOE Layout Manager.
Click on AOE Layout Menu to add layout xml

## Example

```
<reference name="head">
    <block type="core/text" name="helloworld">
        <action method="setText"><text><![CDATA[<script type="text/javascript">alert('Hello World')</script>]]></text></action>
    </block>
</reference>
```

## Debug helper

These blocks help you to find out about the layout handles used on a given page (use handle "default" to enable the helper on every page while debugging):

Add this snippet (using Aoe_LayoutManager) to show all handles in use on a given page added to as HTML comment to the head block:
```
<reference name="head">
    <block type="aoe_layoutManager/layoutHandles" name="aoe_layout_manager.debug" />
</reference>
```

Or as a visible list to the content area:
```
<reference name="content">
    <block type="aoe_layoutManager/layoutHandles" name="aoe_layout_manager.debug">
        <action method="setIsVisible"><param>1</param></action>
    </block>
</reference>
```
