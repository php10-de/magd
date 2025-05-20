create table if not exists ai
(
    ai_id    int unsigned auto_increment
    primary key,
    name     varchar(200)                         not null,
    subtitle varchar(255)                         null,
    init_cmd text                                 null,
    briefing text                                 null,
    greeting varchar(255)                         null,
    active   tinyint(1) unsigned                  not null,
    dupdate  datetime default current_timestamp() not null on update current_timestamp()
    )
    collate = utf8_unicode_ci;

create table if not exists function
(
    function_id int unsigned auto_increment
    primary key,
    ai          int(11) unsigned default 0 not null,
    name        varchar(150)               not null,
    description varchar(255)               not null,
    required    varchar(150)               null,
    constraint function_ibfk_1
    foreign key (ai) references ai (ai_id)
    );

create index if not exists ai
    on function (ai);

create table if not exists function_param
(
    function_param_id int unsigned auto_increment
    primary key,
    function          int(11) unsigned default 0 not null,
    name              varchar(150)               not null,
    property_key      varchar(100)               null,
    property_value    varchar(255)               null,
    constraint function_param_ibfk_1
    foreign key (function) references function (function_id)
    );

create index if not exists ai
    on function_param (function);

create table if not exists gr
(
    gr_id     int unsigned auto_increment
    primary key,
    shortname varchar(100) not null,
    longname  varchar(255) null
    )
    collate = utf8_unicode_ci;

create table if not exists command
(
    command_id int unsigned auto_increment
    primary key,
    ID         varchar(200) not null,
    gr_id      int unsigned null,
    command    varchar(255) not null,
    is_word    tinyint(1)   null,
    lang       char(2)      not null,
    version    int(10)      null,
    constraint command_lang_unique
    unique (command_id, is_word, lang),
    constraint command_group
    foreign key (gr_id) references gr (gr_id)
    on update cascade on delete cascade
    )
    charset = utf8;

create index if not exists gr_id
    on command (gr_id);

create table if not exists dict
(
    dict_id int unsigned auto_increment
    primary key,
    ID      varchar(200) not null,
    gr_id   int unsigned null,
    de      varchar(255) null,
    cn      varchar(255) null,
    en      varchar(255) null,
    gr      varchar(255) null,
    constraint dict_group
    foreign key (gr_id) references gr (gr_id)
    on update cascade on delete set null
    )
    charset = utf8;

create index if not exists gr_id
    on dict (gr_id);

create index if not exists shortname_id
    on gr (shortname);

create table if not exists gr2gr
(
    gr_id1 int unsigned not null,
    gr_id2 int unsigned not null,
    primary key (gr_id1, gr_id2)
    )
    engine = MyISAM
    charset = utf8;

create table if not exists ih_branch
(
    ih_repo_id varchar(255)         not null,
    name       varchar(255)         not null,
    is_active  tinyint(1) default 1 not null,
    primary key (ih_repo_id, name)
    )
    charset = utf8;

create index if not exists ih_repo_url_idx
    on ih_branch (ih_repo_id);

create table if not exists ih_hook
(
    ih_hook_id   int          not null
    primary key,
    ih_repo_id   int          not null,
    ih_branch_id varchar(255) not null
    )
    charset = utf8;

create table if not exists ih_repo
(
    ih_repo_id int(11) unsigned auto_increment
    primary key,
    url        varchar(255) not null,
    username   varchar(200) null,
    password   varchar(100) null
    )
    charset = utf8;

create table if not exists mail_letter
(
    mail_letter_id int unsigned auto_increment
    primary key,
    name           varchar(255)     not null,
    title          varchar(255)     not null,
    html           mediumtext       not null,
    pdf            int(11) unsigned null
    )
    charset = utf8;

create index if not exists pdf
    on mail_letter (pdf);

create table if not exists mail_sender
(
    mail_sender_id int unsigned auto_increment
    primary key,
    from_mail      varchar(200) not null,
    from_name      varchar(200) not null,
    response_mail  varchar(200) not null,
    response_name  varchar(200) not null
    )
    charset = utf8;

create table if not exists mail_group
(
    mail_group_id  int unsigned auto_increment
    primary key,
    name           varchar(200)             not null,
    mail_letter_id int unsigned             null,
    lang           varchar(10) default 'DE' not null,
    sender         int unsigned             not null,
    sql_filter     text                     null,
    is_active      tinyint(1)  default 0    not null,
    is_test        tinyint(1)  default 1    not null,
    optin_url      varchar(250)             null,
    optout_url     varchar(250)             null,
    constraint fk_mail_group_letter
    foreign key (mail_letter_id) references mail_letter (mail_letter_id)
    on delete set null,
    constraint fk_mail_group_sender
    foreign key (sender) references mail_sender (mail_sender_id)
    )
    charset = utf8;

create table if not exists mail
(
    mail_id          int unsigned auto_increment
    primary key,
    mail_group       int(11) unsigned default 0 not null,
    recipient        varchar(100)               not null,
    recipient_name   varchar(100)               null,
    subject          varchar(200)               not null,
    content          text                       not null,
    dsent            datetime                   null,
    attachment_media varchar(255)               null,
    constraint mail_ibfk_1
    foreign key (mail_group) references mail_group (mail_group_id)
    )
    collate = utf8_unicode_ci;

create index if not exists mail_group
    on mail (mail_group);

create index if not exists mail_letter_idx
    on mail_group (mail_letter_id);

create index if not exists sender_idx
    on mail_group (sender);

create table if not exists mail_opt
(
    mail_opt_id   int unsigned auto_increment
    primary key,
    mail_group_id int unsigned                         null,
    mail          varchar(255)                         not null,
    `inout`       enum ('in', 'out')                   not null,
    opt_date      datetime default current_timestamp() not null on update current_timestamp(),
    constraint mail_opt_group_fk
    foreign key (mail_group_id) references mail_group (mail_group_id)
                                                                on update set null on delete set null
    )
    charset = utf8;

create index if not exists mail
    on mail_opt (mail);

create index if not exists mail_idx
    on mail_opt (mail_group_id);

create table if not exists nav
(
    nav_id     int unsigned auto_increment
    primary key,
    to_nav_id  int unsigned     null,
    gr_id      int unsigned     null,
    level      tinyint unsigned not null,
    name       varchar(150)     not null,
    link       varchar(80)      not null,
    params     varchar(200)     null,
    icon       varchar(100)     null,
    icon_color varchar(7)       null,
    constraint fk_gr_id
    foreign key (gr_id) references gr (gr_id)
    on update cascade on delete cascade,
    constraint fk_self
    foreign key (to_nav_id) references nav (nav_id)
    on update set null on delete set null
    )
    charset = utf8;

create index if not exists gr_id
    on nav (gr_id);

create index if not exists link
    on nav (link);

create index if not exists to_nav_id
    on nav (to_nav_id);

create table if not exists notification
(
    notification_id int unsigned auto_increment
    primary key,
    message         varchar(255) not null,
    action          varchar(255) not null
    )
    charset = utf8;

create table if not exists pdf
(
    pdf_id            int unsigned auto_increment
    primary key,
    name              varchar(255) not null,
    html_template     mediumtext   null,
    static_file_media varchar(255) null
    )
    collate = utf8_unicode_ci;

create table if not exists profile
(
    profile_id   int(11) unsigned not null
    primary key,
    profile_name varchar(255)     not null,
    street       varchar(250)     not null,
    city         varchar(250)     not null,
    country      varchar(250)     not null,
    labels       varchar(250)     not null,
    api_key      varchar(64)      null,
    constraint user_id
    unique (profile_id)
    )
    charset = utf8;

create table if not exists augmented
(
    augmented_id int auto_increment
    primary key,
    profile_id   int(11) unsigned                     not null,
    site_url     varchar(255)                         not null,
    selector     varchar(255)                         null,
    html         text                                 null,
    action       text                                 not null,
    active       tinyint(1) unsigned                  not null,
    dupdate      datetime default current_timestamp() not null on update current_timestamp(),
    constraint augmented_profile_constraint
    foreign key (profile_id) references profile (profile_id)
                                                               on update cascade on delete cascade
    )
    charset = utf8;

create index if not exists augmented_user_idx
    on augmented (profile_id);

create table if not exists projects
(
    projects_id int unsigned auto_increment
    primary key
);

create table if not exists r
(
    right_id  int unsigned auto_increment
    primary key,
    shortname varchar(255) not null,
    longname  varchar(255) null
    )
    engine = MyISAM
    charset = utf8;

create index if not exists longname_id
    on r (longname);

create index if not exists shortname_id
    on r (shortname);

create table if not exists red_button
(
    red_button_id int unsigned auto_increment
    primary key,
    tablename     varchar(200) not null,
    filename      varchar(255) null,
    replace_from  text         null,
    replace_to    text         null,
    patch         text         null,
    commit_id     varchar(200) null,
    is_active     tinyint(1)   null,
    error         varchar(255) null,
    constraint file_commit_unique
    unique (filename, commit_id)
    )
    charset = utf8;

create table if not exists red_button_conf
(
    red_button_conf_id     int unsigned auto_increment
    primary key,
    red_button_entity_name varchar(255) not null,
    red_button_data_name   varchar(255) null,
    param                  varchar(255) not null,
    value                  varchar(255) null,
    constraint red_button_conf_index
    unique (red_button_entity_name, red_button_data_name, param)
    )
    charset = latin1;

create table if not exists red_button_entity
(
    red_button_entity_id int unsigned auto_increment
    primary key,
    entity_name          varchar(255)                                                      not null,
    data_name            varchar(255)                                                      null,
    data_type            enum ('int', 'numeric', 'string', 'ckb', 'date', 'blob', 'media') null,
    is_nullable          tinyint(1) unsigned                                               null,
    after                varchar(255)                                                      null,
    constraint entity_data
    unique (entity_name, data_name)
    )
    charset = latin1;

create table if not exists right2gr
(
    right_id int unsigned not null,
    gr_id    int unsigned not null,
    yn       tinyint      not null,
    constraint right_id
    unique (right_id, gr_id),
    constraint right_id_2
    unique (right_id, gr_id),
    constraint right_id_3
    unique (right_id, gr_id)
    )
    engine = MyISAM
    charset = utf8;

create index if not exists gr_id
    on right2gr (gr_id);

create table if not exists right2gr_log
(
    right_id       int unsigned     not null,
    gr_id          int unsigned     not null,
    yn             tinyint unsigned not null,
    update_user_id int(11) unsigned not null,
    dbupdate       datetime         not null
    )
    engine = MyISAM
    charset = utf8;

create index if not exists dict_id
    on right2gr_log (gr_id, right_id);

create index if not exists update_user_id
    on right2gr_log (update_user_id);

create table if not exists right2user
(
    right_id int unsigned not null,
    user_id  int unsigned not null,
    yn       tinyint      not null,
    primary key (right_id, user_id)
    )
    engine = MyISAM
    charset = utf8;

create table if not exists right2user_log
(
    right_id       int unsigned     not null,
    user_id        int unsigned     not null,
    yn             tinyint unsigned not null,
    update_user_id int(11) unsigned not null,
    dbupdate       datetime         not null
    )
    engine = MyISAM
    charset = utf8;

create index if not exists dict_id
    on right2user_log (user_id, right_id);

create index if not exists update_user_id
    on right2user_log (update_user_id);

create table if not exists setting
(
    id    varchar(100) not null
    primary key,
    value text         not null,
    gr_id int unsigned not null
    )
    engine = MyISAM
    charset = utf8;

create index if not exists gr_id
    on setting (gr_id);

create table if not exists user
(
    user_id    int unsigned auto_increment
    primary key,
    email      varchar(50)               not null,
    password   varchar(40)               not null,
    firstname  varchar(255) charset utf8 not null,
    lastname   varchar(255)              not null,
    is_active  tinyint(1) default 1      not null,
    login_link varchar(255)              null,
    dbinsert   datetime                  not null,
    lang       char(2)                   not null,
    dbupdate   datetime                  null comment 'for disabling email function.',
    constraint email
    unique (email)
    )
    charset = latin1;

create table if not exists chat_history
(
    chat_history_id int(11) unsigned auto_increment
    primary key,
    user_id         int unsigned     default 0 not null,
    ai_id           int(11) unsigned default 0 not null,
    session_id      varchar(255)               not null,
    human           varchar(255)               not null,
    ai              mediumtext                 not null,
    action          varchar(255)               null,
    cdate           datetime                   not null,
    constraint chat_history_ibfk_1
    foreign key (ai_id) references ai (ai_id),
    constraint chat_history_user_id_foreign_key
    foreign key (user_id) references user (user_id)
    on update cascade on delete cascade
    );

create index if not exists ai_id
    on chat_history (ai_id);

create index if not exists session_id
    on chat_history (session_id);

create table if not exists user2gr
(
    user_id int unsigned not null,
    gr_id   int unsigned not null,
    primary key (user_id, gr_id)
    )
    engine = MyISAM
    charset = utf8;

create table if not exists user2gr_log
(
    gr_id          int unsigned     not null,
    user_id        int unsigned     not null,
    yn             tinyint unsigned not null,
    update_user_id int(11) unsigned not null,
    dbupdate       datetime         not null
    )
    engine = MyISAM
    charset = utf8;

create index if not exists dict_id
    on user2gr_log (user_id, gr_id);

create index if not exists update_user_id
    on user2gr_log (update_user_id);

