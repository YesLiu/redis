#redis 一致性hash 理解

一致性哈希算法在1997年由麻省理工学院提出，设计目标是为了解决因特网中的热点(Hot spot)问题，初衷和CARP十分类似。一致性哈希修正了CARP使用的简单哈希算法带来的问题，使得DHT可以在P2P环境中真正得到应用。

理解：

假如有三个redis  a b c

得到 a b c 对应的hash  假设 a = 1 b=5 c=9

现在要存的redis 数据  key = value 

得到key 对应的hash  假设 得到的hash 为  key =4  

存入规则为 将key 存入第一个hash值大于key的hash值 的redis中 

循环 1 5 9  如果 大于4 返回 对应的redis  

如果key的值大于任何redis hash值 存入第一个redis。

