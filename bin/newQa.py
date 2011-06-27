#!/usr/bin/env python
#
# Original filename: bin/newQa.py
#
# Author: 
# Email: 
# Date: Thu 2011-06-09 14:00:22
# 
# Summary: 
# 
"""
%prog [options] name
"""

import sys, os, re, glob
import optparse

haveEups = True
try:
    import eups
except:
    haveEups = False
    

#############################################################
#
# Main body of code
#
#############################################################

def main(qaName, wwwRoot=None, force=False):

    # verify that we have WWW_ROOT and TESTING_DISPLAYQA_DIR
    envVars = ['TESTING_DISPLAYQA_DIR']
    if wwwRoot is None:
        envVars.append('WWW_ROOT')
    missing = []
    for envVar in envVars:
        if not os.environ.has_key(envVar):
            missing.append(envVar)
    if len(missing) > 0:
        raise Exception("Missing environment variable(s):\n", "\n".join(missing))
    
    if wwwRoot is None:
        wwwRoot = os.environ['WWW_ROOT']
    dqaDir = os.environ['TESTING_DISPLAYQA_DIR']


    # get our version
    if haveEups:
        e = eups.Eups()
        version, eupsPathDir, productDir, tablefile, flavour = e.findSetupVersion('testing_displayQA')
    else:
        path, v = os.path.split(dqaDir)
        if re.search("^v[\d.-_]+$", v):
            version = v
        else:
            version = "working-copy-%s" % (v)


    # check to see if qaname already exists with different version
    dest = os.path.join(wwwRoot, qaName)

    if os.path.exists(dest):
        vFile = os.path.join(dest, "version")
        if os.path.exists(vFile):
            fp = open(vFile)
            v = fp.readlines()[0]
            v = v.strip()
            fp.close()

            if not force:
                if (v != version):
                    raise Exception(qaName,
                                    "already exists with different version. Use -f to force.")
                else:
                    print "QA site '" + qaName + "' already exists at:"
                    print "    ", dest
                    print "Exiting (nothing done)."
                    sys.exit()
            
    else:
        os.mkdir(dest)
        fp = open(os.path.join(dest, "version"), 'w')
        fp.write("%s\n" % version)
        fp.close()


                
    # copy the www/ to the destination
    src = os.path.join(dqaDir, "www")
    patterns = ["php", "css", "ico"]
    files = []
    for p in patterns:
        files += glob.glob(os.path.join(src, "[a-zA-Z]*." + p))
    doc = os.path.join(dqaDir, "doc")
    files += glob.glob(os.path.join(doc, "README"))

    for f in files:
        print "installing: ", f
        cmd = "cp -r %s %s" % (f, dest)
        os.system(cmd)

    print ""
    print "Created new QA site served from:"
    print "   ",dest


#############################################################
# end
#############################################################

if __name__ == '__main__':
    parser = optparse.OptionParser(usage=__doc__)
    parser.add_option("-f", '--force', default=False, action="store_true",
                      help="Force install if versions differ (default=%default)")
    parser.add_option('-r', '--root', default=None, help="Override WWW_ROOT (default=%default")
    opts, args = parser.parse_args()

    if len(args) != 1:
        parser.print_help()
        sys.exit(1)

    qaName, = args
    
    main(qaName, opts.root, opts.force)
    
