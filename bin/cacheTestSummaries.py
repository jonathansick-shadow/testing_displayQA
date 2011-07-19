#!/usr/bin/env python
#
# Original filename: bin/cacheTestSummaries.py
#
# Author: 
# Email: 
# Date: Thu 2011-06-09 14:00:22
# 
# Summary: 
# 
"""
%prog [options] rerun ntestAdjust npassAdjust

This program is used to adjust the number of tests and passed tests in the cache.
It's only purpose to create a count cache for older runs which preceded caching.
"""

import sys, os, re, glob
import optparse
import datetime

haveEups = True
try:
    import eups
except:
    haveEups = False
    
import lsst.testing.displayQA as dispQa

#############################################################
#
# Main body of code
#
#############################################################

def main(qaName, ntestAdjust, npassAdjust, wwwRoot=None, force=False):

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

    # verify the rerun exists
    rerunDir = os.path.join(wwwRoot, qaName)
    if not os.path.exists(rerunDir):
        print "No such rerun: ", rerunDir
        sys.exit()

    # get a list of tests
    testList = glob.glob(os.path.join(rerunDir, "test_*"))

    testSetInfo = []
    for test in testList:
        path, basename = os.path.split(test)
        m = re.search("^test_([^_.]+)_([^_.]+)\.([^_]+)$", basename)
        group, alias, label = None, None, None
        if m:
            group, alias, label = m.groups()
        if (not group is None) and (not alias is None) and (not label is None):
            testSetInfo.append([group, alias, label])

        # handle the top-level (ie. nameless) group
        group, alias, label = None, None, None
        m = re.search("^test__([^_.]+)\.([^_]+)$", basename)
        if m:
            alias, label = m.groups()
            group = ""
        if (not group is None) and (not alias is None) and (not label is None):
            testSetInfo.append([group, alias, label])

    # for each test, create a testSet, and recache things
    print "Adjusting: "
    print "WWW_ROOT: ", wwwRoot
    print "WWW_RERUN: ", qaName

    # change this internally as TestSet will use it.
    os.environ['WWW_RERUN'] = qaName

    for i in range(len(testSetInfo)):
        group, alias, label = testSetInfo[i]
        ts = dispQa.TestSet(label=label, group=group, alias=alias, wwwCache=True)
        ntest, npass, dataset, oldest, newest, extras = \
               ts.updateCounts(increment=[int(ntestAdjust), int(npassAdjust)])
        if len(group.strip()) == 0:
            group = "top-level"
        print "%-12s %-24s " % (group, label)

        if oldest > 0 and newest > 0:
            oldest = datetime.datetime.fromtimestamp(int(oldest)).strftime('%Y-%m-%d %H:%M:%S')
            newest = datetime.datetime.fromtimestamp(int(newest)).strftime('%Y-%m-%d %H:%M:%S')
        
        print "  ", "npass/ntest = %d/%d  %s  %s %s  %s" % (npass, ntest, dataset, oldest, newest, extras)


#############################################################
# end
#############################################################

if __name__ == '__main__':
    parser = optparse.OptionParser(usage=__doc__)
    parser.add_option("-f", '--force', default=False, action="store_true",
                      help="Force install if versions differ (default=%default)")
    parser.add_option("-p", "--npass", default=0, type=int,
                      help="Adjust npass by this amount (default=%default)")
    parser.add_option('-r', '--root', default=None, help="Override WWW_ROOT (default=%default")
    parser.add_option("-t", "--ntest", default=0, type=int,
                      help="Adjust ntest by this amount (default=%default)")
    opts, args = parser.parse_args()

    if len(args) != 1:
        parser.print_help()
        sys.exit(1)

    qaName, = args
    
    main(qaName, opts.ntest, opts.npass, opts.root, opts.force)
    
