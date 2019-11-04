const rootFolder = { name: 'Главная', id: 0 }
export default {
  tree (folders) {
    const list = [{ ...rootFolder }, ...folders.map(folder => ({ ...folder, id: +folder.id, parent_id: +folder.parent_id }))]
    let map = {}
    let node
    let roots = []
    let i
    for (i = 0; i < list.length; i += 1) {
      map[list[i].id] = i // initialize the map
      list[i].children = [] // initialize the children
    }
    for (i = 0; i < list.length; i += 1) {
      node = list[i]
      if (!isNaN(+node.parent_id) && list[map[node.parent_id]]) {
        list[map[node.parent_id]].children.push(node)
      } else {
        roots.push(node)
      }
    }
    return roots[0]
  },
  getTreePath (id, folders, path = '/catalogs') {
    const tree = this.tree(folders)
    const _findInTree = (id, tree) => {
      if (+tree.id === +id) {
        let path = [{
          name: tree.name,
          id: tree.id
        }]
        return path
      } else {
        for (let child of tree.children) {
          let path = _findInTree(id, child)
          if (path && path.length) {
            path.unshift({
              name: tree.name,
              id: +tree.id
            })
            return path
          }
        }
        return []
      }
    }
    if (this.tree) {
      return [{ ...rootFolder }, ..._findInTree(id, tree).slice(1)].map(folder => {
        const query = {}
        if (folder.id) query.parent_id = folder.id
        return {
          ...folder,
          link: {
            path,
            query
          }
        }
      })
    } else {
      return []
    }
  }
}
