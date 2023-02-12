#include <bits/stdc++.h>
using namespace std;
int main()
{
    int a, b, c, mn, mx;
    cin >> a >> b >> c;

    // Minimun Value
    if (a <= b)
        mn = a;
    else
        mn = b;
    if (mn > c)
        mn = c;

    // Maximum Value
    if (a >= b)
        mx = a;
    else
        mx = b;
    if (mx < c)
        mx = c;
    cout << "Minimum Value is " << mn << ' ' << "Maximum Value is " << mx;
    // cout << min(min(a, b), c) << ' ' << max(max(a, b), c);
}
