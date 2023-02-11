#include <bits/stdc++.h>
using namespace std;
int main(){
    int a,b,c;
    cin >> a >> b >> c;
    if(a == b && b == c){cout << a;}
    else {
        if(a <=b){
            if(a<=c)cout << a;
        }
        if (b<=c){
            if(b<=a)cout << b;
        }
        if(c<=a)if(c<=b)cout << c;
    }
}
