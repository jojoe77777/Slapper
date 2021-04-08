# Slapper
<a href="https://discord.gg/n4hANRh"><img src="https://discordapp.com/api/guilds/412491783486832640/embed.png" alt="Discord server"/></a>

The new home of Slapper, the NPC plugin for PocketMine-MP.

## NOTE
This plugin is designed for **[PocketMine-MP](https://github.com/pmmp/PocketMine-MP)** only and we do not support any other variations, forks or spoons.
Compatibility with unofficial variants can occasionally be found, but **do not expect support if you are using anything other than PocketMine-MP**.

## Addons

Official addons:
- [SlapBack](https://github.com/jojoe77777/SlapBack)
- [SlapperRotation](https://github.com/jojoe77777/SlapperRotation)
- [SlapperCache](https://github.com/jojoe77777/SlapperCache)
- [SlapperPlus](https://github.com/jojoe77777/SlapperPlus)
- [SlapperCooldown](https://github.com/jojoe77777/SlapperCooldown)



# Basic documentation

## Commands:

- /slapper [args...]
- /rca <player> <command> - Run command as another player! This can be used to only run the command if the player has permission.

## Main level commands:
- help: /slapper help
- spawn: /slapper spawn <type> [name]
- edit: /slapper edit [id] [args...]
- id: /slapper id
- remove: /slapper remove [id]
- version: /slapper version
- cancel: /slapper cancel
- updateall: /slapper updateall

### Edit args:
- helmet: /slapper edit <eid> helmet <id>
- chestplate: /slapper edit <eid> <id>
- leggings: /slapper edit <eid> leggings <id>
- boots: /slapper edit <eid> boots <id>
- skin: /slapper edit <eid> skin
- name: /slapper edit <eid> name <name>
- addcommand: /slapper edit <eid> addcommand <command>
- delcommand: /slapper edit <eid> delcommand <command>
- listcommands: /slapper edit <eid> listcommands
- update: /slapper edit <eid> update
- block: /slapper edit <eid> block <id>
- tphere: /slapper edit <eid> tphere
- tpto: /slapper edit <eid> tpto
- menuname: /slapper edit <eid> menuname <name/remove>
	
	
### Aliases for edit args
helmet: helm, helmet, head, hat, cap
chestplate: chest, shirt, chestplate
leggings: pants, legs, leggings
boots: feet, boots, shoes
item: hand, item, holding, arm, held
skin: setskin, changeskin, editskin, skin
name: name, customname
menuname: listname, nameonlist, menuname
namevisible: namevisible, customnamevisible, tagvisible, name_visible, custom_name_visible, tag_visible
addcommand: addc, adduced, add command
delcommand: delc, delcmd, delcommand, remove command
listcommands: listcommands, listcmds, listcs
fix: update, fix, migrate
block: block, tile, blockid, tileid
tphere: teleporthere, tphere, movehere, bringer
tpto: teleportto, tpto, goto, teleport, tp
