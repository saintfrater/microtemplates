# MicroTemplates

## Intro

Microtemplate is a php class for html template.

Like many templating system, I need a way to distinct php-code from html.
I've first setup a simple `{variable}` replace method (`str_replace`) and a file loader.

Then cames the trouble, I want to implement `include` files... then a some how macro language cames...

I never pretend that this class is for everyone, I've decided to build it for somes personnals reasons.

**Purposes:**

..* Build a simple an robust system
..* Stay comprehensive for beginners
..* Studdy more POO under PHP (yes I'm an old non-POO dev)
..* experiments some macro languages developpements...

## Commands - template language

They are few commands already implemented, there is no comments 
For the moment a command begin with a *#* and end with a *;*...

### pre-process commands

`#basepath (path);` this command setup an basepath (may be *templates/* or anything)
`#include (file[,file,...]);` include files.

### post-process (render) commands

`{variable_name}` : replace any *variable_name* by it's values

## Commands - php methods

### template files loading and management

any template file is loaded base on a private variable (`basepath`) and the filename.

`MicroTemlate::load($file)` : clear all previous loaded template and load new file
`MicroTemlate::load_include($file)` : append to any previously loaded template the new file
`MicroTemlate::set_path($path)` : set *basepath* variable.
`MicroTemlate::get_path($path)` : return *basepath* variable.

### variables assignement

`MicroTemlate::assign($variablename,$value)` : assign any value to a variable, existing variables'values are replaced, you should not add {} to the variable name.
