# Open Graph Metas - A MyBB plugin
A very primitive MyBB plugin for showing Open Graph metas in forum/thread/etc. modules.

## Features
- Output Open Graph metas for various forum modules. Read the Open Graph protocol: https://ogp.me/.
- No template editing requires.
- Search-engine-friendly-URLs-aware `og:url`.
- Customizable fallback description for `og:description`, _now via file editing_.
- Customizable fallback image for `og:image`, _now via file editing_.
- Customizable `og:image` for threads/posts, _now via file editing_.
- Supported modules:
  - **Forums**:
    - `og:description`: the forum's description or fallback description.
    - `og:image`: fallback image or current viewer's theme logo. 
  - **Threads** in linear view mode:
    - `og:description`: the message content of the first post in current viewing page.
    - `og:image` _(selectable via file editing)_:
      - images attached to posts in thread, _attachments_;
      - (*) images embedded in posts in thread, using _\[img\] tag_;
      - the avatar of the first post author's;
      - fallback image. 
  - **Posts** / Threads in threaded view mode:
    - `og:description`: the message content of current post.
    - `og:image` _(selectable via file editing)_:
      - images attached to current post, _attachments_;
      - (*) images embedded in current post, using _\[img\] tag_;
      - current post author's avatar;
      - fallback image. 
  - **Member profiles**:
    - `og:description`: the member's signature, if applicable.
    - `og:image`: the member's avatar.
  - _**Other pages not fully supported**_:
    - **Portal**
    - **Member list**
    - **Search**
    - **Calendar**
    - **Help**

### Note and caution
 - (*) features not implemented yet.
 - **Images** that use attachments/thumnails **for threads and posts** are served by using MyBB's attachment script, which **are not static files**.


## Requirement
- MyBB 1.8.x

## Installation
1. Download & unzip.
1. Upload files under **`Upload/`** folder, please maintain the folder structure.
1. Turn to MyBB's AdminCP > Configuration > Plugins, find **Open Graph Metas** and activate it.

## Uninstall & Removal
1. Turn to MyBB's AdminCP > Configuration > Plugins, find **Open Graph Metas** and deactivate it.
1. Remove file **`./inc/plugins/open_graph_metas.php`** from your server.

## Upgrade Notice
- This plugin tends to get changed before its stable release. Please use with caution.
- Plugin name may be changed to **Social Integrations** or sort of, if more related social integrations for information sharing, for example Twitter Card Tags, have been added.
- Folder structure may change during version upgrades.