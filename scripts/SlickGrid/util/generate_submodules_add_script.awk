#
# GAWK script to extract both path and submodule URI definitions from a .gitmodules file.
#
#
# [submodule "php/lib/ultimatemysql"]
#    path = php/lib/ultimatemysql
#    url = git@github.com:SlickGrid/ultimatemysql.git
#
# -->
#
# git submodule add  git@github.com:SlickGrid/ultimatemysql.git  php/lib/ultimatemysql
#


BEGIN {
    printf("#! /bin/bash\n");
    printf("# generated by collect_git_add_recursively.sh\n");
    printf("\n");
    printf("pushd $(dirname $0)                                                                                     2> /dev/null   > /dev/null\n");
    printf("cd ..\n");
    printf("\n");
    printf("\n");
    printf("mode=\"R\"\n");
    printf("\n");
    printf("Win7DEV_BASEDIR=/media/sf_D_DRIVE/h/prj/1original/SlickGrid/SlickGrid\n");
    printf("\n");
    printf("function git_submod_add {\n");
    printf("    git submodule  add $1 $2\n");
    printf("    if test \"$mode\" = \"W\" ; then\n");
    printf("        git_register_remote_for_UnixVM $1 $2\n");
    printf("    fi\n");
    printf("}\n");
    printf("\n");
    printf("function git_register_remote_for_UnixVM {\n");
    printf("    if test -d \"$2\" ; then\n");
    printf("        if test -d \"$Win7DEV_BASEDIR/$2\" ; then\n");
    printf("            pushd $2                                                                                    2> /dev/null  > /dev/null\n");
    printf("            git remote remove Win7DEV\n");
    printf("            git remote add Win7DEV $Win7DEV_BASEDIR/$2\n");
    printf("            popd                                                                                        2> /dev/null  > /dev/null\n");
    printf("        fi\n");
    printf("    fi\n");
    printf("}\n");
    printf("\n");
    printf("getopts \":Wh\" opt\n");
    printf("#echo opt+arg = \"$opt$OPTARG\"\n");
    printf("case \"$opt$OPTARG\" in\n");
    printf("W )\n");
    printf("  echo \"--- registering Win7DEV as remote ---\"\n");
    printf("  mode=\"W\"\n");
    printf("  for (( i=OPTIND; i > 1; i-- )) do\n");
    printf("    shift\n");
    printf("  done\n");
    printf("  #echo args: $@\n");
    printf("  if test -d \"$1\" ; then\n");
    printf("    Win7DEV_BASEDIR=$1\n");
    printf("  fi\n");
    printf("  #\n");
    printf("  # register Win7DEV remote for main repo as well!\n");
    printf("  if test -d \"$Win7DEV_BASEDIR\" ; then\n");
    printf("    git remote remove Win7DEV\n");
    printf("    git remote add Win7DEV $Win7DEV_BASEDIR\n");
    printf("  fi\n");
    printf("  ;;\n");
    printf("\n");
    printf("\"?\" )\n");
    printf("  echo \"--- registering git submodules ---\"\n");
    printf("  mode=\"R\"\n");
    printf("  ;;\n");
    printf("\n");
    printf("* )\n");
    printf("  cat <<EOT\n");
    printf("$0 [-W <optional_remote_path>]\n");
    printf("\n");
    printf("set up git submodules / submodule references for all submodules.\n");
    printf("\n");
    printf("-W       : set up 'Win7DEV' remote reference per submodule\n");
    printf("\n");
    printf("           When you don't specify the remote path yourself,\n");
    printf("           the default is set to:\n");
    printf("             \"$Win7DEV_BASEDIR\"\n");
    printf("\n");
    printf("EOT\n");
    printf("  exit\n");
    printf("  ;;\n");
    printf("esac\n");
    printf("\n");
    printf("\n");
    printf("\n");
    printf("\n");

    state = 0;
    idx = 0;
}

/\[submodule/       {
    # because MSys gawk doesn't support match() with 3 arguments :-((
    split($0, a, "\"");
    submodule_path = a[2];
    #printf("Selecting path [%s]\n", submodule_path);
    next;
}

/path = /       {
    # because MSys gawk doesn't support match() with 3 arguments :-((
    split($0, a, "=");
    submodule_path = a[2];
    #printf("Selecting path [%s]\n", submodule_path);
    next;
}

/url = /        {
    # because MSys gawk doesn't support match() with 3 arguments :-((
    split($0, a, "=");
    submodule_uri = a[2];
    #printf("Selecting URI [%s]\n", submodule_uri);

    stmts[++idx] = sprintf("git_submod_add  %-70s  %s", submodule_uri, submodule_path);
    #printf("# id %d: %s\n", idx, stmts[idx]);
    next;
}

            {
    next;
}

END             {
    asort(stmts);
    for (i = 1; i <= idx; i++)
    {
        printf("%s\n", stmts[i]);
    }
    printf("\n");
    printf("\n");
    printf("\n");
    printf("popd                                                                                                    2> /dev/null   > /dev/null\n");
    printf("\n");
}

