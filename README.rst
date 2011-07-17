=============================
Diag PukiWiki Extension
=============================

requirement
===========
pukiwiki >= 1.4.6
blockdiag (for "block" of type)
nwdiag    (for "nw" of type)
seqdiag   (for "seq" of type)
actdiag   (for "act" of type)

install
===========

1. Copy diag.inc.php to ${PUKIWIKI_ROOT}/plugin/ ::

    $ cp diag.inc.php ${PUKIWIKI_ROOT}/plugin/

2. Change line to ${PUKIWIKI_ROOT}/pukiwiki.ini.php ::

    - define('PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK', 1); // 1 = Disabled
    + define('PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK', 0); // 1 = Disabled

example
===========

- blockdiag ::

    #diag(block){{
    diagram {
      A -> B -> C -> D;
      A -> E -> F -> G;
    }
    }}

- nwdiag ::

    #diag(nw){{
    diagram {
      network dmz {
        address = "210.x.x.x/24"

        web01 [address = "210.x.x.1"];
        web02 [address = "210.x.x.2"];
      }
      network internal {
        address = "172.x.x.x/24";

        web01 [address = "172.x.x.1"];
        web02 [address = "172.x.x.2"];
        db01;
        db02;
      }
    }
    }}

- seqdiag ::

    #diag(seq){{
    diagram {
      browser  -> webserver [label = "GET /index.html"];
      browser <-- webserver;
      browser  -> webserver [label = "POST /blog/comment"];
      webserver  -> database [label = "INSERT comment"];
      webserver <-- database;
      browser <-- webserver;
    }
    }}

- actdiag ::

    #diag(act){{
    diagram {
      write -> convert -> image

      lane user {
        label = "User"
        write [label = "Writing reST"];
        image [label = "Get diagram IMAGE"];
      }
      lane actdiag {
        convert [label = "Convert reST to Image"];
      }
    }
    }}

