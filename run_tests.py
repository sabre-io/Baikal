import os, sys, importlib.util, traceback, mechanicalsoup
from tests.test_helpers import BASE_URL, ADMIN_PASSWORD

FAILED = False

def remove_config():
    yaml_path = "config/baikal.yaml"
    if os.path.exists(yaml_path):
        try:
            with open(yaml_path, "r", encoding="utf-8") as f:
                content = f.read()
            if "abbc0ffed774e98a495fe5a2596ed7deab14211f250673ad661be7b759c9a7fd" not in content:
                # If this is contained, it is a test file that can safely be deleted.
                # Keep in sync with mod.ADMIN_PASSWORD
                assert False, "There already is a config file that was not created by a test. Stopping."
                
            os.remove(yaml_path)
        except Exception as e:
            print("Error while checking baikal.yaml:", e)
            sys.exit(1)

def load_tests(path):
    files = []
    for r, _, f in os.walk(path):
        for x in f:
            if x.endswith(".py"):
                files.append(os.path.join(r, x))
    assert files != []
    return sorted(files)

def run_file(path):
    global FAILED
    test_folder = os.path.dirname(os.path.abspath(path))
    if test_folder not in sys.path:
        sys.path.insert(0, test_folder)
        
    spec = importlib.util.spec_from_file_location("mod", path)
    mod = importlib.util.module_from_spec(spec)
    
    try:
        spec.loader.exec_module(mod)
    except:
        print("ERR in import:", path)
        FAILED = True
        traceback.print_exc()
        return

    setup_function = None
    for s in dir(mod):
        if s == "setup":
            setup_function = getattr(mod, s)

    for n in dir(mod):
        if not n.startswith("test_"):
            continue
        print(path, "::", n, sep = '')
        remove_config()
    
        browser = mechanicalsoup.StatefulBrowser()
        test_function = getattr(mod, n)
        try:
            setup_function()
            test_function(browser)
            print("[OK]")
        except:
            FAILED = True
            traceback.print_exc()
            print(browser.get_current_page())
            print("[FAIL]: ", path, "::", n, sep = '')

def main():
    global FAILED
    
    if len(sys.argv) > 1:
        run_file(sys.argv[1])
    else:
        for f in load_tests("tests"):
            run_file(f)

    if FAILED:
        print("Some tests failed.")
        sys.exit(1)
    else:
        print("All tests passed.")
        sys.exit(0)

if __name__ == "__main__":
    main()
