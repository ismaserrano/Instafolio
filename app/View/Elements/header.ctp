<div id="header-container">
        <header class="wrapper">
                <a href="<?= $this->Html->url("/"); ?>"><h1 id="title">Instafolio</h1><img id="logo" src="<?= $this->Html->url("/"); ?>img/logo.png" alt="Instafolio Logo" title="Instafolio Logo" /></a>
                <nav>
                        <ul>
                                <?
                                if ($this->Session->check('instagram.userId')){
                                ?>
                                <li><a href="<?= $this->Html->url("/logout"); ?>" class="rounded-top-corners">Logout</a></li>
                                <li><a href="#" class="rounded-top-corners<? if(strpos($this->here, 'social')){ ?> active<? } ?>">Social (soon)</a></li>
                                <li class="profile"><a href="<?= $this->Html->url("/users"); ?>" class="rounded-top-corners<? if(strpos($this->here, 'users') && !strpos($this->here, 'social')){ ?> active<? } ?>">My account</a></li>
                                <?
                                } else {
                                ?>
                                <li><a id="instagram-login" href="<?= $this->Html->url("/users/login"); ?>" class="rounded-top-corners">Sign in with Instagram</a></li>
                                <?
                                }
                                ?>
                                <li><a href="<?= $this->Html->url("/about-us"); ?>" class="rounded-top-corners<? if(strpos($this->here, 'about-us')){ ?> active<? } ?>">About us</a></li>
                                <li><a href="<?= $this->Html->url("/"); ?>" class="rounded-top-corners<? if(strpos($this->here, 'home') || strpos($this->here, '/')){ ?> active<? } ?>">Home</a></li>
                        </ul>
                </nav>
        </header>
</div>