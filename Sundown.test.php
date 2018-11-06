<?php

namespace Deasilsoft;

include_once "Sundown.class.php";

$sundown = new Sundown();

echo $sundown->convert("


Big Header
============



[[YouTube]](M7lc1UVf-VE)

[[vimeo]](M7lc1UVf-VE)

[[TWITCH]](M7lc1UVf-VE)



![Alt Text](http://imagehost.com/image.png \"Some Title, YO!\")

!<[Alt Text 2](http://imagehost.com/image2.png)



![Alt Text][Test Caption](http://imagehost.com/image.png \"Some Title, YO!\")

!>[Alt Text 2][Test Caption 2](http://imagehost.com/image2.png)



[Test Link](http://google.com/ \"This leads to google!\")

[Test Link](http://google.com/)



Paragraph, upcoming empty linebreak test...  

  

``` Javascript


    function TestScript() {
        
        alert(\"This is very annoying, please stop.\");
        
    }
    
    # Made easy by Sundown
    
    **NO SUNDOWN WILL BE MATCHED HERE**
    
    
```



> Test Quote  
> Continues
New Paragraph (NOT PART OF QUOTE)



===



# This is a heading



This ~~is a~~ paragraph.  



This is the same paragraph.



This is a new paragraph.



* This
* is
* a



 SURPRISE  



 THIS  
 IS  
 AWESOME
* list



This is *a __TEST__ new __\*TEST\*__* **paragraph _TEST_**.  
This is the `**same**` paragraph, but different.



**SIMPLE STRONG, with _emphasis_**



1. LOL
2. DOGGO



   1. Test



   2. Test
500. This is three.



*** ^^(Test ^^(Description ^^(Can Go Deep))) List Title, [Google link](http://google.com)
++ !^^^^(**Description List**) Item
++ !^^Description List Item
-- Description List Item
-- Description List Item  
*** Description List Title
*** Description **List** Title
++ Description List Item
-- Description List Item  
   And it can also

   Go on and on and on



| Test |
| One Column |
| Suddenly | Two |
| Then Back |
| To |
| One Column |
| And | Suddenly | Three |



| Pretty Table | With Pretty Columns |
| ============ | =================== |
|     Test     |>        Right       |
|     Test     |<        Left        |
|     Test     |>        Right       |
|     Test     |<        Left        |
|     Test     |>        Right       |
|     Test     |<        Left        |



| ====== | Header | Header | Header | Header | Header |
| ====== | ====== | ====== | ====== | ====== | ====== |
| Header |  Cell  |  Cell  |  Cell  |  Cell  |  Cell  |
| Header |  Cell  |  Cell  |  Cell  |  Cell  |  Cell  |
| Header |  Cell  |  Cell  |  Cell  |  Cell  |  Cell  |
| Header |  Cell  |  Cell  |  Cell  |  Cell  |  Cell  |
| Header |  Cell  |  Cell  |  Cell  |  Cell  |  Cell  |
| Header |  Cell  |  Cell  |  Cell  |  Cell  |  Cell  |
| Header |  Cell  |  Cell  |  Cell  |  Cell  |  Cell  |
| Header |  Cell  |  Cell  |  Cell  |  Cell  |  Cell  |
| Header |  Cell  |  Cell  |  Cell  |  Cell  |  Cell  |



This is a new paragraph.  



This is how you use ``CTRL + K``... You get {HTML}(Hyper-Text Markup Language).  


");
