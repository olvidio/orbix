normal 1G
:%s/base64_encode/core\\urlsafe_b64encode/
:%s/base64_decode/core\\urlsafe_b64decode/
wq
