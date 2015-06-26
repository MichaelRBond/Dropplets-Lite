Dropplets Lite
===============

Strips out features of Dropplets that I didn't want and makes it more minimal. I am also working on refactoring and optimizing code to improve speed and maintainability. 

For more information on Dropplets check out the [Dropplets GitHub page](https://github.com/Circa75/dropplets). 

## Changes from Original Dropplets:
1. Removed all features that required a login have been removed. Upload posts directly to posts folder
1. Simple Template has been updated to include support for a header image
1. Added default config.php and .htaccess files
1. Removed phpass dependancy
1. Refactoring. 


## Getting Started
- [Installation](#installation)
- [Writing Posts](#writing-posts)
- [Publishing Posts](#publishing-posts)
- [License](#license)

## Installation
Dropplets Lite is compatible with most server configurations and can be typically installed in under a minute using the few step-by-step instructions below:

1. Clone the github repo
3. Upload the entire contents of the extracted zip file to your web server wherever you want Dropplets to be installed. 
4. rename dot-htaccess to .htaccess 
5. open up config.php and modify the settings as needed. 

## Writing Posts
With Dropplets, you write your posts offline (using the text or Markdown editor of your choice) in Markdown format. Here's a handy [syntax guide](https://github.com/circa75/dropplets/wiki/Markdown-Syntax-Guide) if you need a little help with your Markdown skills. All posts for Dropplets **MUST** be composed using the following format:

    # Your Post Title
    - Post Author Name (e.g. "Dropplets")
    - Post Author Twitter Handle (e.g. "dropplets")
    - Publish Date in YYYY/MM/DD Format (e.g. "2013/04/28")
    - Post Category (e.g. "Random Thoughts")
    - Post Status (e.g. "published" or "draft")

    Your post text starts here. 
    
All posts must also be saved with the **.md** file extension. For instance, if your post title is **My First Blog Post**, your post file should look like this:

    my-first-blog-post.md

Some templates include the ability to add a post image or thumbnail along with your post in which should match your post file name like this:

    my-first-blog-post.jpg

Post file names are used to structure post permalinks on your blog. So, a post file saved as **my-first-blog-post.md** will result in **yoursite.com/my-first-blog-post** as the post permalink.

## Publishing Posts
After you've finished writing your post offline, you can then publish your post:

1. Upload the post and, optionally, the thumbnail to the posts directory inside your Dropplets Lite installation. 

If caching is turned on, you will need to clear the cache (./posts/cache) before changes are viewable. 

## Original Dropplet License
Copyright (c) 2013 Circa75 Media, LLC

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.